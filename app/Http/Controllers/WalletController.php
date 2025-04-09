<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TheSieuReService;
use App\Models\CardDeposit;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletController extends Controller
{
    /**
     * Hiển thị thông tin ví của người dùng
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Hiển thị lịch sử giao dịch của ví
     */
    public function transactions()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Hiển thị trang nạp tiền vào ví
     */
    public function deposit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        // Tạo mã đơn hàng nạp tiền
        $depositCode = 'WALLET-' . time() . rand(1000, 9999);
        
        // Lưu vào session để sử dụng cho callback
        session(['deposit' => [
            'code' => $depositCode,
            'amount' => 0, // Sẽ được cập nhật khi người dùng chọn số tiền
            'created_at' => now()
        ]]);
        
        return view('wallet.deposit', [
            'wallet' => $wallet,
            'depositCode' => $depositCode,
        ]);
    }
    
    /**
     * Xử lý chọn số tiền để nạp qua chuyển khoản
     */
    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $amount = $request->input('amount');
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        // Cập nhật thông tin nạp tiền trong session
        $deposit = session('deposit', []);
        $deposit['amount'] = $amount;
        session(['deposit' => $deposit]);
        
        // Tạo nội dung thanh toán cho QR
        $pattern = config('payment.pattern', 'SEVQR');
        $depositCode = $deposit['code'];
        $cleanedCode = str_replace('WALLET-', '', $depositCode);
        $paymentContent = $pattern . ' ORDWALLET' . $cleanedCode;
        
        // Tạo QR code cho thanh toán
        $qrUrl = "https://qr.sepay.vn/img?acc=103870429701&bank=VietinBank&amount={$amount}&des=" . urlencode($paymentContent) . "&template=compact";
        
        // Log thông tin QR để debug
        \Illuminate\Support\Facades\Log::info('SePay QR Nạp Ví', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'deposit_code' => $depositCode,
            'payment_content' => $paymentContent,
            'amount' => $amount
        ]);
        
        // Tạo thông tin thanh toán để hiển thị
        $paymentInfo = [
            'qr_url' => $qrUrl,
            'payment_content' => $paymentContent,
            'amount' => $amount,
            'deposit_code' => $depositCode
        ];
        
        return view('wallet.deposit_confirm', [
            'paymentInfo' => $paymentInfo,
            'wallet' => $wallet
        ]);
    }
    
    /**
     * Xử lý webhook khi nhận được thanh toán nạp ví thành công
     * Phương thức này được gọi từ WebhookController khi nhận webhook từ SePay
     */
    public static function processDepositWebhook($userId, $amount, $transactionData, $depositCode)
    {
        /** @var \App\Models\User $user */
        $user = User::find($userId);
        if (!$user) {
            \Illuminate\Support\Facades\Log::error('Không tìm thấy người dùng khi xử lý nạp ví', [
                'user_id' => $userId, 
                'deposit_code' => $depositCode
            ]);
            return false;
        }
        
        try {
            // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
            $wallet = $user->wallet()->first();
            if (!$wallet) {
                $wallet = $user->wallet()->create([
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Tạo giao dịch nạp tiền
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->balance_before = $wallet->balance;
            $transaction->balance_after = $wallet->balance + $amount;
            $transaction->type = WalletTransaction::TYPE_DEPOSIT;
            $transaction->description = "Nạp tiền vào ví qua chuyển khoản ngân hàng";
            $transaction->reference_id = null;
            $transaction->reference_type = 'bank_transfer';
            $transaction->metadata = is_array($transactionData) ? json_encode($transactionData) : $transactionData;
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance = $transaction->balance_after;
            $wallet->save();
            
            \Illuminate\Support\Facades\Log::info('Đã nạp tiền vào ví thành công', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'new_balance' => $wallet->balance,
                'deposit_code' => $depositCode
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi khi xử lý nạp tiền vào ví', [
                'user_id' => $user->id,
                'deposit_code' => $depositCode,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Xử lý nạp tiền bằng thẻ cào
     */
    public function depositCard(Request $request)
    {
        $request->validate([
            'telco' => 'required|string',
            'amount' => 'required|numeric',
            'serial' => 'required|string',
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        $telco = $request->input('telco');
        $amount = $request->input('amount');
        $serial = $request->input('serial');
        $code = $request->input('code');
        
        // Tạo request_id duy nhất
        $requestId = 'CARD-' . time() . rand(1000, 9999);
        
        // Gọi API TheSieuRe
        $theSieuReService = new TheSieuReService();
        
        // Lưu thông tin thẻ cào vào cơ sở dữ liệu trước khi gửi API
        $cardDeposit = CardDeposit::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'telco' => $telco,
            'amount' => $amount,
            'serial' => $serial,
            'code' => $code,
            'request_id' => $requestId,
            'status' => 'pending',
            'actual_amount' => $theSieuReService->calculateActualAmount($telco, $amount)
        ]);
        
        // Gửi request đến TheSieuRe
        $response = $theSieuReService->chargeCard($telco, $code, $serial, $amount, $requestId);
        
        if (!$response['success']) {
            // Cập nhật trạng thái thẻ cào khi API lỗi
            $cardDeposit->update([
                'status' => 'failed',
                'response' => json_encode($response),
            ]);
            
            return redirect()->route('wallet.deposit')
                ->with('error', 'Có lỗi xảy ra khi nạp thẻ. Vui lòng thử lại sau.');
        }
        
        $result = $response['data'];
        
        // Cập nhật trạng thái thẻ cào dựa trên kết quả từ API
        $status = $result['status'] ?? 99;
        $statusMessage = $result['message'] ?? 'PENDING';
        
        // Cập nhật thông tin trong database
        $cardDeposit->update([
            'trans_id' => $result['trans_id'] ?? null,
            'status' => $this->mapCardStatus($status),
            'response' => json_encode($result),
        ]);
        
        // Nếu thẻ được xử lý thành công ngay lập tức
        if ($status == 1) {
            // Thực hiện cộng tiền vào ví
            $actualAmount = $theSieuReService->calculateActualAmount($telco, $amount);
            
            // Tạo giao dịch nạp tiền
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = $actualAmount;
            $transaction->balance_before = $wallet->balance;
            $transaction->balance_after = $wallet->balance + $actualAmount;
            $transaction->type = WalletTransaction::TYPE_DEPOSIT;
            $transaction->description = "Nạp tiền thành công từ thẻ $telco mệnh giá " . number_format($amount) . "đ";
            $transaction->reference_id = $cardDeposit->id;
            $transaction->reference_type = 'card_deposit';
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance = $transaction->balance_after;
            $wallet->save();
            
            // Cập nhật trạng thái thẻ
            $cardDeposit->update([
                'status' => 'completed',
                'actual_amount' => $actualAmount,
                'completed_at' => now()
            ]);
            
            return redirect()->route('wallet.index')
                ->with('success', "Nạp thẻ thành công! Số tiền " . number_format($actualAmount) . "đ đã được thêm vào ví của bạn.");
        }
        
        // Nếu thẻ đang chờ xử lý
        if ($status == 99) {
            return view('wallet.card_pending', [
                'cardDeposit' => $cardDeposit,
                'wallet' => $wallet
            ]);
        }
        
        // Nếu thẻ bị từ chối
        return redirect()->route('wallet.deposit')
            ->with('error', "Nạp thẻ không thành công. " . $statusMessage);
    }

    /**
     * Kiểm tra trạng thái thẻ cào
     */
    public function checkCardStatus($requestId)
    {
        $cardDeposit = CardDeposit::where('request_id', $requestId)->first();
        
        if (!$cardDeposit) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin nạp thẻ'
            ]);
        }
        
        // Nếu thẻ đã hoàn thành hoặc thất bại, trả về kết quả luôn
        if (in_array($cardDeposit->status, ['completed', 'failed'])) {
            return response()->json([
                'success' => $cardDeposit->status === 'completed',
                'status' => $cardDeposit->status,
                'message' => $cardDeposit->status === 'completed' 
                    ? 'Thẻ đã được nạp thành công' 
                    : 'Thẻ nạp không thành công',
                'actual_amount' => $cardDeposit->actual_amount
            ]);
        }
        
        // Nếu đang chờ xử lý, gọi API kiểm tra
        $theSieuReService = new TheSieuReService();
        $response = $theSieuReService->checkCard($cardDeposit->request_id);
        
        if (!$response['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái thẻ. Vui lòng thử lại sau.'
            ]);
        }
        
        $result = $response['data'];
        $status = $result['status'] ?? 99;
        
        // Cập nhật trạng thái trong database
        $cardStatus = $this->mapCardStatus($status);
        $cardDeposit->status = $cardStatus;
        $cardDeposit->response = json_encode($result);
        
        // Nếu thẻ đã được xử lý xong
        if ($status == 1) {
            $user = Auth::user();
            
            // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
            $wallet = $user->wallet()->first();
            if (!$wallet) {
                $wallet = $user->wallet()->create([
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Tính toán số tiền thực
            $actualAmount = $theSieuReService->calculateActualAmount($cardDeposit->telco, $cardDeposit->amount);
            $cardDeposit->actual_amount = $actualAmount;
            $cardDeposit->completed_at = now();
            
            // Chỉ cộng tiền vào ví nếu chưa được cộng trước đó
            if (!$cardDeposit->transaction_id) {
                // Tạo giao dịch nạp tiền
                $transaction = new WalletTransaction();
                $transaction->wallet_id = $wallet->id;
                $transaction->amount = $actualAmount;
                $transaction->balance_before = $wallet->balance;
                $transaction->balance_after = $wallet->balance + $actualAmount;
                $transaction->type = WalletTransaction::TYPE_DEPOSIT;
                $transaction->description = "Nạp tiền thành công từ thẻ {$cardDeposit->telco} mệnh giá " . number_format($cardDeposit->amount) . "đ";
                $transaction->reference_id = $cardDeposit->id;
                $transaction->reference_type = 'card_deposit';
                $transaction->save();
                
                // Cập nhật số dư ví
                $wallet->balance = $transaction->balance_after;
                $wallet->save();
                
                // Lưu transaction_id vào cardDeposit
                $cardDeposit->transaction_id = $transaction->id;
            }
        }
        
        $cardDeposit->save();
        
        return response()->json([
            'success' => $cardStatus === 'completed',
            'status' => $cardStatus,
            'message' => $this->getCardStatusMessage($cardStatus),
            'actual_amount' => $cardDeposit->actual_amount
        ]);
    }

    /**
     * Hiển thị trang trạng thái thẻ đang chờ xử lý
     */
    public function showCardPending($requestId)
    {
        $cardDeposit = CardDeposit::where('request_id', $requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        return view('wallet.card_pending', [
            'cardDeposit' => $cardDeposit,
            'wallet' => $wallet
        ]);
    }

    /**
     * Map trạng thái thẻ từ API sang định dạng trong hệ thống
     */
    private function mapCardStatus($status)
    {
        switch ($status) {
            case 1: // Thành công
                return 'completed';
            case 2: // Thẻ sai hoặc đã sử dụng
            case 3: // Thẻ không đúng mệnh giá
            case 4: // Hệ thống bảo trì
                return 'failed';
            case 99: // Đang xử lý
            default:
                return 'pending';
        }
    }

    /**
     * Lấy thông báo trạng thái thẻ
     */
    private function getCardStatusMessage($status)
    {
        switch ($status) {
            case 'completed':
                return 'Thẻ đã được nạp thành công';
            case 'failed':
                return 'Thẻ nạp không thành công';
            case 'pending':
            default:
                return 'Thẻ đang được xử lý';
        }
    }

    /**
     * Xử lý depositCallback sau khi nạp tiền thành công
     */
    public function depositCallback(Request $request)
    {
        // Lấy thông tin từ session hoặc từ database
        $depositInfo = session('deposit');
        
        if (!$depositInfo) {
            return redirect()->route('wallet.deposit')->with('error', 'Không tìm thấy thông tin nạp tiền.');
        }
        
        // Xác thực thời gian hiệu lực của yêu cầu nạp tiền
        $createdAt = Carbon::parse($depositInfo['created_at']);
        if ($createdAt->diffInHours(now()) > 24) {
            // Xóa thông tin session sau khi xử lý xong
            session()->forget('deposit');
            return redirect()->route('wallet.deposit')->with('error', 'Yêu cầu nạp tiền đã hết hạn. Vui lòng tạo yêu cầu mới.');
        }
        
        $amount = $depositInfo['amount'];
        $code = $depositInfo['code'];
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        // Kiểm tra xem giao dịch đã được xử lý trước đó chưa
        $existingTransaction = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', WalletTransaction::TYPE_DEPOSIT)
            ->where('metadata->deposit_code', $code)
            ->first();
            
        if ($existingTransaction) {
            // Nếu giao dịch đã tồn tại, không xử lý nữa
            session()->forget('deposit');
            return redirect()->route('wallet.index')
                ->with('info', 'Giao dịch nạp tiền này đã được xử lý trước đó.');
        }
        
        // Cập nhật số dư và ghi log giao dịch
        try {
            DB::beginTransaction();
            
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->balance_before = $wallet->balance;
            $transaction->balance_after = $wallet->balance + $amount;
            $transaction->type = WalletTransaction::TYPE_DEPOSIT;
            $transaction->description = 'Nạp tiền vào ví qua chuyển khoản SePay';
            $transaction->reference_id = null;
            $transaction->reference_type = 'bank_transfer';
            $transaction->metadata = json_encode([
                'request' => $request->all(),
                'deposit_code' => $code
            ]);
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance = $transaction->balance_after;
            $wallet->save();
            
            DB::commit();
            
            // Log thông tin nạp tiền thành công
            \Illuminate\Support\Facades\Log::info('Nạp tiền vào ví thành công qua callback', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'deposit_code' => $code,
                'transaction_id' => $transaction->id
            ]);
            
            // Xóa thông tin session sau khi xử lý xong
            session()->forget('deposit');
            
            return redirect()->route('wallet.index')
                ->with('success', 'Nạp tiền thành công! Số tiền ' . number_format($amount) . 'đ đã được thêm vào ví của bạn.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log lỗi
            \Illuminate\Support\Facades\Log::error('Lỗi khi nạp tiền vào ví qua callback', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'deposit_code' => $code,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('wallet.deposit')
                ->with('error', 'Có lỗi xảy ra khi xử lý nạp tiền. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.');
        }
    }
}
