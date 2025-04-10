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
use App\Models\WalletDeposit;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class WalletController extends Controller
{
    /**
     * Hiển thị thông tin ví của người dùng
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng
        $wallet = $user->getWallet();
        
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
        
        // Lấy ví của người dùng
        $wallet = $user->getWallet();
        
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
        $wallet = $user->getWallet();
        
        // Tạo mã đơn hàng nạp tiền
        $depositCode = WalletDeposit::generateDepositCode();
        
        // Tạo bản ghi deposit trong database
        $walletDeposit = WalletDeposit::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'deposit_code' => $depositCode,
            'status' => WalletDeposit::STATUS_PENDING,
        ]);
        
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
        $depositCode = $request->input('deposit_code');
        
        // Tìm bản ghi deposit trong database
        $walletDeposit = WalletDeposit::where('deposit_code', $depositCode)
            ->where('user_id', $user->id)
            ->where('status', WalletDeposit::STATUS_PENDING)
            ->firstOrFail();
        
        // Lấy ví của người dùng
        $wallet = $user->getWallet();
        
        // Cập nhật số tiền trong bản ghi deposit
        $walletDeposit->amount = $amount;
        
        // Tạo nội dung thanh toán cho QR
        $pattern = config('payment.pattern', 'SEVQR');
        $cleanedCode = str_replace('WALLET-', '', $depositCode);
        $paymentContent = $pattern . ' ORDWALLET' . $cleanedCode;
        
        // Cập nhật nội dung thanh toán
        $walletDeposit->payment_content = $paymentContent;
        $walletDeposit->save();
        
        // Tạo QR code cho thanh toán
        $qrUrl = "https://qr.sepay.vn/img?acc=103870429701&bank=VietinBank&amount={$amount}&des=" . urlencode($paymentContent) . "&template=compact";
        
        // Log thông tin QR để debug
        Log::info('SePay QR Nạp Ví', [
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
        
        // Tạo đối tượng depositOrder cho view
        $depositOrder = (object)[
            'order_number' => $depositCode,
            'amount' => $amount,
            'status' => 'pending'
        ];
        
        return view('wallet.deposit_checkout', [
            'paymentInfo' => $paymentInfo,
            'wallet' => $wallet,
            'depositOrder' => $depositOrder
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
            Log::error('Không tìm thấy người dùng khi xử lý nạp ví', [
                'user_id' => $userId, 
                'deposit_code' => $depositCode
            ]);
            return false;
        }
        
        try {
            // Kiểm tra xem giao dịch đã được xử lý chưa dựa vào transaction_id từ SePay
            $transactionId = $transactionData['id'] ?? null;
            if ($transactionId) {
                $existingTransaction = WalletTransaction::where('metadata->id', $transactionId)
                    ->where('type', WalletTransaction::TYPE_DEPOSIT)
                    ->first();
                
                if ($existingTransaction) {
                    Log::info('Giao dịch nạp tiền đã được xử lý trước đó', [
                        'user_id' => $userId,
                        'transaction_id' => $transactionId,
                        'deposit_code' => $depositCode
                    ]);
                    return true; // Trả về true vì đã xử lý thành công trước đó
                }
            }
            
            // Tìm bản ghi deposit trong database
            $walletDeposit = WalletDeposit::where('deposit_code', $depositCode)
                ->where('user_id', $userId)
                ->where('status', WalletDeposit::STATUS_PENDING)
                ->first();
                
            if (!$walletDeposit) {
                Log::warning('Không tìm thấy bản ghi nạp tiền đang chờ xử lý', [
                    'user_id' => $userId,
                    'deposit_code' => $depositCode
                ]);
                
                // Tạo mới bản ghi nếu không tìm thấy
                $wallet = $user->getWallet();
                $walletDeposit = WalletDeposit::create([
                    'user_id' => $userId,
                    'wallet_id' => $wallet->id,
                    'deposit_code' => $depositCode,
                    'amount' => $amount,
                    'status' => WalletDeposit::STATUS_PENDING,
                    'payment_content' => $transactionData['content'] ?? null,
                    'transaction_id' => $transactionId
                ]);
            }
            
            // Lấy ví của người dùng
            $wallet = $user->getWallet();
            
            // Sử dụng phương thức deposit có sẵn trong model Wallet
            $transaction = $wallet->deposit(
                $amount,
                WalletTransaction::TYPE_DEPOSIT,
                "Nạp tiền vào ví qua chuyển khoản ngân hàng",
                $walletDeposit->id,  // referenceId
                'wallet_deposit', // referenceType
                $transactionData // metadata
            );
            
            // Cập nhật trạng thái deposit
            $walletDeposit->status = WalletDeposit::STATUS_COMPLETED;
            $walletDeposit->transaction_id = $transactionId;
            $walletDeposit->completed_at = Carbon::now();
            $walletDeposit->metadata = is_array($transactionData) ? $transactionData : json_decode($transactionData, true);
            $walletDeposit->save();
            
            Log::info('Đã nạp tiền vào ví thành công', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'new_balance' => $wallet->balance,
                'deposit_code' => $depositCode,
                'transaction_id' => $transaction->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý nạp tiền vào ví', [
                'user_id' => $user->id,
                'deposit_code' => $depositCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Lấy ví của người dùng (sử dụng phương thức getWallet để đồng nhất với luồng nạp tiền)
        $wallet = $user->getWallet();
        
        $telco = $request->input('telco');
        $amount = $request->input('amount');
        $serial = $request->input('serial');
        $code = $request->input('code');
        
        // Tạo request_id duy nhất
        $requestId = CardDeposit::generateRequestId();
        
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
            'status' => CardDeposit::STATUS_PENDING,
            'actual_amount' => $theSieuReService->calculateActualAmount($telco, $amount)
        ]);
        
        // Gửi request đến TheSieuRe
        $response = $theSieuReService->chargeCard($telco, $code, $serial, $amount, $requestId);
        
        if (!$response['success']) {
            // Cập nhật trạng thái thẻ cào khi API lỗi
            $cardDeposit->status = CardDeposit::STATUS_FAILED;
            $cardDeposit->response = $response;
            $cardDeposit->save();
            
            return redirect()->route('wallet.deposit')
                ->with('error', 'Có lỗi xảy ra khi nạp thẻ. Vui lòng thử lại sau.');
        }
        
        $result = $response['data'];
        
        // Cập nhật trạng thái thẻ cào dựa trên kết quả từ API
        $status = $result['status'] ?? 99;
        $statusMessage = $result['message'] ?? 'PENDING';
        
        // Cập nhật thông tin trong database
        $cardStatus = $this->mapCardStatus($status);
        $cardDeposit->trans_id = $result['trans_id'] ?? null;
        $cardDeposit->status = $cardStatus;
        $cardDeposit->response = $result;
        $cardDeposit->save();
        
        // Nếu thẻ được xử lý thành công ngay lập tức
        if ($status == 1) {
            // Thực hiện cộng tiền vào ví
            $actualAmount = $theSieuReService->calculateActualAmount($telco, $amount);
            
            // Tạo giao dịch nạp tiền sử dụng phương thức deposit() của model Wallet
            $transaction = $wallet->deposit(
                $actualAmount,
                WalletTransaction::TYPE_DEPOSIT,
                "Nạp tiền thành công từ thẻ $telco mệnh giá " . number_format($amount) . "đ",
                $cardDeposit->id,
                'card_deposit',
                ['provider' => 'thesieure', 'telco' => $telco, 'card_amount' => $amount]
            );
            
            // Cập nhật trạng thái thẻ
            $cardDeposit->status = CardDeposit::STATUS_COMPLETED;
            $cardDeposit->actual_amount = $actualAmount;
            $cardDeposit->completed_at = now();
            $cardDeposit->save();
            
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
        if ($cardDeposit->isCompleted() || $cardDeposit->isFailed()) {
            return response()->json([
                'success' => $cardDeposit->isCompleted(),
                'status' => $cardDeposit->status,
                'message' => $cardDeposit->isCompleted() 
                    ? 'Nạp thẻ thành công! Số tiền đã được cộng vào ví của bạn.'
                    : 'Nạp thẻ thất bại. ' . $this->getCardStatusMessage($cardDeposit)
            ]);
        }
        
        // Nếu thẻ đang chờ xử lý, kiểm tra với API
        $theSieuReService = new TheSieuReService();
        $response = $theSieuReService->checkCardStatus($cardDeposit->request_id);
        
        if (!$response['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái thẻ. Vui lòng thử lại sau.'
            ]);
        }
        
        $result = $response['data'];
        $status = $result['status'] ?? 99;
        
        // Cập nhật trạng thái thẻ cào theo kết quả từ API
        $cardStatus = $this->mapCardStatus($status);
        $cardDeposit->status = $cardStatus;
        $cardDeposit->response = $result;
        
        // Nếu thẻ được xử lý thành công
        if ($status == 1) {
            $actualAmount = $theSieuReService->calculateActualAmount($cardDeposit->telco, $cardDeposit->amount);
            
            // Lấy ví của người dùng
            $user = $cardDeposit->user;
            $wallet = $user->getWallet();
            
            // Tạo giao dịch nạp tiền sử dụng phương thức deposit() của model Wallet
            $transaction = $wallet->deposit(
                $actualAmount,
                WalletTransaction::TYPE_DEPOSIT,
                "Nạp tiền thành công từ thẻ {$cardDeposit->telco} mệnh giá " . number_format($cardDeposit->amount) . "đ",
                $cardDeposit->id,
                'card_deposit',
                ['provider' => 'thesieure', 'telco' => $cardDeposit->telco, 'card_amount' => $cardDeposit->amount]
            );
            
            // Cập nhật trạng thái thẻ
            $cardDeposit->status = CardDeposit::STATUS_COMPLETED;
            $cardDeposit->actual_amount = $actualAmount;
            $cardDeposit->completed_at = now();
            $cardDeposit->save();
            
            return response()->json([
                'success' => true,
                'status' => CardDeposit::STATUS_COMPLETED,
                'message' => 'Nạp thẻ thành công! Số tiền đã được cộng vào ví của bạn.'
            ]);
        }
        
        // Nếu thẻ bị từ chối
        if ($status == 2 || $status == 3) {
            $cardDeposit->status = CardDeposit::STATUS_FAILED;
            $cardDeposit->save();
            
            return response()->json([
                'success' => false,
                'status' => CardDeposit::STATUS_FAILED,
                'message' => 'Nạp thẻ thất bại. ' . $this->getCardStatusMessage($cardDeposit)
            ]);
        }
        
        // Nếu thẻ vẫn đang chờ xử lý
        $cardDeposit->save();
        
        return response()->json([
            'success' => false,
            'status' => CardDeposit::STATUS_PENDING,
            'message' => 'Thẻ đang được xử lý. Vui lòng chờ trong giây lát.'
        ]);
    }

    /**
     * Chuyển đổi mã trạng thái từ API sang trạng thái của hệ thống
     */
    private function mapCardStatus($status)
    {
        switch ($status) {
            case 1: // Thành công
                return CardDeposit::STATUS_COMPLETED;
            case 2: // Thẻ sai
            case 3: // Lỗi
                return CardDeposit::STATUS_FAILED;
            case 99: // Chờ xử lý
            default:
                return CardDeposit::STATUS_PENDING;
        }
    }

    /**
     * Lấy thông báo trạng thái thẻ dựa trên response
     */
    private function getCardStatusMessage($cardDeposit)
    {
        $response = is_array($cardDeposit->response) ? $cardDeposit->response : json_decode($cardDeposit->response, true);
        
        if (isset($response['message'])) {
            return $response['message'];
        }
        
        switch ($cardDeposit->status) {
            case CardDeposit::STATUS_COMPLETED:
                return 'Thẻ đã được nạp thành công.';
            case CardDeposit::STATUS_FAILED:
                return 'Thẻ không hợp lệ hoặc đã được sử dụng.';
            case CardDeposit::STATUS_PENDING:
                return 'Thẻ đang được xử lý.';
            default:
                return 'Không xác định được trạng thái thẻ.';
        }
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
        
        // Lấy ví của người dùng (sử dụng phương thức getWallet)
        $wallet = $user->getWallet();
        
        return view('wallet.card_pending', [
            'cardDeposit' => $cardDeposit,
            'wallet' => $wallet
        ]);
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
        
        // Lấy ví của người dùng
        $wallet = $user->getWallet();
        
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
            Log::info('Nạp tiền vào ví thành công qua callback', [
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
            Log::error('Lỗi khi nạp tiền vào ví qua callback', [
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
