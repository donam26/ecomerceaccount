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
use App\Models\Wallet;
use App\Services\SePayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
     * Lưu thông tin giao dịch nạp tiền vào database
     */
    private function saveWalletDeposit($userId, $walletId, $depositCode, $paymentContent, $amount)
    {
        try {
            // Lưu thông tin vào database
            DB::table('wallet_deposits')->insert([
                'user_id' => $userId,
                'wallet_id' => $walletId,
                'deposit_code' => $depositCode,
                'payment_content' => $paymentContent,
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi khi lưu thông tin nạp tiền', [
                'user_id' => $userId,
                'deposit_code' => $depositCode,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Xử lý form nạp tiền vào ví (bước 2)
     */
    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:100000000',
            'payment_method' => 'required|in:bank_transfer,card',
        ]);
        
        $user = Auth::user();
        $amount = $request->input('amount');
        $paymentMethod = $request->input('payment_method');
        
        // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'is_active' => true,
            ]);
        }
        
        if ($paymentMethod === 'card') {
            return redirect()->route('wallet.deposit.card');
        }
        
        // Tạo mã nạp tiền duy nhất
        $depositCode = 'WALLET-' . time() . rand(1000, 9999);
        
        // Tạo nội dung thanh toán
        $paymentContent = config('payment.pattern', 'SEVQR') . ' ORDWALLET' . str_replace('WALLET-', '', $depositCode);
        
        // Tạo QR code từ dịch vụ SePay
        $qrService = new SePayService();
        $qrUrl = $qrService->generateQrCode($amount, $paymentContent);
        
        // Lưu thông tin vào session để sử dụng cho callback
        session(['deposit' => [
            'code' => $depositCode,
            'amount' => $amount,
            'payment_content' => $paymentContent,
            'created_at' => now()->timestamp
        ]]);
        
        // Lưu thông tin vào cơ sở dữ liệu
        $this->saveWalletDeposit($user->id, $wallet->id, $depositCode, $paymentContent, $amount);
        
        // Lưu thông tin vào logs để có thể truy xuất sau này
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
            Log::error('Không tìm thấy người dùng khi xử lý nạp ví', [
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
            $transaction->metadata = json_encode([
                'deposit_code' => $depositCode,
                'data' => $transactionData
            ]);
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance = $transaction->balance_after;
            $wallet->save();
            
            // Cập nhật bảng wallet_deposits nếu tồn tại
            try {
                // Kiểm tra xem có bản ghi wallet_deposits nào với deposit_code này không
                $deposit = DB::table('wallet_deposits')
                    ->where('deposit_code', $depositCode)
                    ->first();
                
                if ($deposit) {
                    DB::table('wallet_deposits')
                        ->where('deposit_code', $depositCode)
                        ->update([
                            'status' => 'completed',
                            'transaction_id' => $transaction->id,
                            'response' => is_array($transactionData) ? json_encode($transactionData) : $transactionData,
                            'completed_at' => now()
                        ]);
                }
            } catch (\Exception $e) {
                // Lỗi khi cập nhật bảng wallet_deposits không ảnh hưởng đến kết quả nạp tiền
                Log::warning('Không thể cập nhật bảng wallet_deposits', [
                    'deposit_code' => $depositCode,
                    'error' => $e->getMessage()
                ]);
            }
            
            Log::info('Đã nạp tiền vào ví thành công', [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'new_balance' => $wallet->balance,
                'deposit_code' => $depositCode
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý nạp tiền vào ví', [
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
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
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
     * Hiển thị trang chờ xử lý thẻ cào
     */
    public function showCardPending($requestId)
    {
        $user = Auth::user();
        
        // Tìm thông tin thẻ cào
        $cardDeposit = CardDeposit::where('request_id', $requestId)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        return view('wallet.card_pending', compact('cardDeposit'));
    }
    
    /**
     * Kiểm tra trạng thái thẻ cào
     */
    public function checkCardStatus($requestId)
    {
        $user = Auth::user();
        
        // Tìm thông tin thẻ cào
        $cardDeposit = CardDeposit::where('request_id', $requestId)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Nếu thẻ đã hoàn thành hoặc thất bại, trả về trạng thái hiện tại
        if ($cardDeposit->status !== 'pending') {
            return response()->json([
                'success' => true,
                'status' => $cardDeposit->status,
                'message' => $cardDeposit->status == 'completed' 
                    ? 'Thẻ đã được nạp thành công' 
                    : 'Thẻ nạp không hợp lệ hoặc đã được sử dụng'
            ]);
        }
        
        // Gọi API kiểm tra trạng thái thẻ cào
        $theSieuReService = new TheSieuReService();
        $response = $theSieuReService->checkCard($requestId);
        
        if (!$response['success']) {
            return response()->json([
                'success' => false, 
                'message' => 'Không thể kiểm tra trạng thái thẻ. Vui lòng thử lại sau.'
            ]);
        }
        
        $result = $response['data'];
        $status = $result['status'] ?? 99;
        
        // Xử lý kết quả từ API
        if ($status == 1) {
            // Thẻ đúng - gạch thẻ thành công
            // Cập nhật trạng thái thẻ cào
            $cardDeposit->status = 'completed';
            $cardDeposit->trans_id = $result['trans_id'] ?? null;
            $cardDeposit->response = json_encode($result);
            $cardDeposit->completed_at = now();
            
            // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Sử dụng phương thức deposit của ví để nạp tiền và tạo giao dịch
            $transaction = $wallet->deposit(
                $cardDeposit->actual_amount,
                WalletTransaction::TYPE_DEPOSIT,
                'Nạp tiền qua thẻ cào ' . $cardDeposit->getTelcoNameAttribute() . ' mệnh giá ' . number_format($cardDeposit->amount) . ' VNĐ',
                $cardDeposit->id,
                'card_deposit',
                [
                    'telco' => $cardDeposit->telco,
                    'amount' => $cardDeposit->amount,
                    'actual_amount' => $cardDeposit->actual_amount,
                    'serial' => $cardDeposit->serial,
                    'request_id' => $cardDeposit->request_id,
                    'trans_id' => $cardDeposit->trans_id
                ]
            );
            
            // Lưu ID giao dịch vào bản ghi thẻ cào
            $cardDeposit->transaction_id = $transaction->id;
            $cardDeposit->save();
            
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Thẻ đã được nạp thành công',
                'redirect' => route('wallet.index')
            ]);
        } elseif ($status == 2) {
            // Thẻ đúng - đang chờ xử lý
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Thẻ đúng và đang chờ xử lý. Vui lòng đợi trong giây lát.'
            ]);
        } elseif ($status == 3) {
            // Thẻ sai mệnh giá
            // Cập nhật trạng thái thẻ cào
            $cardDeposit->status = 'completed';
            $cardDeposit->trans_id = $result['trans_id'] ?? null;
            $cardDeposit->response = json_encode($result);
            $cardDeposit->completed_at = now();
            
            // Tính lại số tiền thực tế
            $actualAmount = $result['amount'] ?? 0;
            $cardDeposit->actual_amount = $theSieuReService->calculateActualAmount($cardDeposit->telco, $actualAmount);
            
            // Lấy ví của người dùng (hoặc tạo mới nếu chưa có)
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Sử dụng phương thức deposit của ví để nạp tiền và tạo giao dịch
            $transaction = $wallet->deposit(
                $cardDeposit->actual_amount,
                WalletTransaction::TYPE_DEPOSIT,
                'Nạp tiền qua thẻ cào ' . $cardDeposit->getTelcoNameAttribute() . ' (sai mệnh giá, thực tế: ' . number_format($actualAmount) . ' VNĐ)',
                $cardDeposit->id,
                'card_deposit',
                [
                    'telco' => $cardDeposit->telco,
                    'amount' => $cardDeposit->amount,
                    'actual_amount' => $cardDeposit->actual_amount,
                    'real_amount' => $actualAmount,
                    'serial' => $cardDeposit->serial,
                    'request_id' => $cardDeposit->request_id,
                    'trans_id' => $cardDeposit->trans_id,
                    'wrong_amount' => true
                ]
            );
            
            // Lưu ID giao dịch vào bản ghi thẻ cào
            $cardDeposit->transaction_id = $transaction->id;
            $cardDeposit->save();
            
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Thẻ sai mệnh giá nhưng đã được nạp thành công với giá trị thực tế ' . number_format($actualAmount) . ' VNĐ',
                'redirect' => route('wallet.index')
            ]);
        } elseif ($status == 4) {
            // Thẻ không đúng hoặc đã được sử dụng
            $cardDeposit->status = 'failed';
            $cardDeposit->response = json_encode($result);
            $cardDeposit->completed_at = now();
            $cardDeposit->save();
            
            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => 'Thẻ không đúng hoặc đã được sử dụng',
                'redirect' => route('wallet.deposit')
            ]);
        } else {
            // Các trường hợp khác
            return response()->json([
                'success' => false,
                'status' => 'pending',
                'message' => $theSieuReService->getStatusMessage($status) ?? 'Đang kiểm tra thẻ. Vui lòng đợi trong giây lát.'
            ]);
        }
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
     * Xử lý callback khi nạp tiền hoàn tất
     */
    public function depositCallback(Request $request)
    {
        // Lấy thông tin nạp tiền từ session
        $deposit = $request->session()->get('wallet_deposit');
        
        if (!$deposit) {
            return redirect()->route('wallet.deposit')
                ->with('error', 'Không tìm thấy thông tin nạp tiền');
        }
        
        return view('wallet.deposit_pending', [
            'deposit' => $deposit
        ]);
    }

    /**
     * Xử lý webhook từ TheSieuRe khi thẻ cào được xử lý
     */
    public function theSieuReWebhook(Request $request)
    {
        // Ghi log dữ liệu webhook
        Log::info('TheSieuRe Webhook Data', [
            'data' => $request->all()
        ]);
        
        // Kiểm tra dữ liệu đầu vào
        $requestId = $request->input('request_id');
        $status = intval($request->input('status'));
        $amount = $request->input('amount', 0);
        $transId = $request->input('trans_id');
        
        if (!$requestId) {
            return response()->json(['status' => 'error', 'message' => 'Thiếu thông tin request_id']);
        }
        
        // Tìm thông tin thẻ cào
        $cardDeposit = CardDeposit::where('request_id', $requestId)->first();
        if (!$cardDeposit) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Không tìm thấy thông tin thẻ cào'
            ]);
        }
        
        // Nếu thẻ đã được xử lý trước đó, trả về thành công để tránh xử lý lại
        if ($cardDeposit->status !== 'pending') {
            return response()->json([
                'status' => 'success', 
                'message' => 'Thẻ cào đã được xử lý trước đó'
            ]);
        }
        
        // Lưu phản hồi từ API
        $response = $request->all();
        $cardDeposit->response = json_encode($response);
        $cardDeposit->trans_id = $transId;
        
        // Xử lý theo trạng thái thẻ
        if ($status == 1) {
            // Thẻ đúng - gạch thẻ thành công
            $cardDeposit->status = 'completed';
            $cardDeposit->completed_at = now();
            
            // Lấy ví của người dùng
            $user = User::find($cardDeposit->user_id);
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Sử dụng phương thức deposit của ví để nạp tiền và tạo giao dịch
            $transaction = $wallet->deposit(
                $cardDeposit->actual_amount,
                WalletTransaction::TYPE_DEPOSIT,
                'Nạp tiền qua thẻ cào ' . $cardDeposit->getTelcoNameAttribute() . ' mệnh giá ' . number_format($cardDeposit->amount) . ' VNĐ',
                $cardDeposit->id,
                'card_deposit',
                [
                    'telco' => $cardDeposit->telco,
                    'amount' => $cardDeposit->amount,
                    'actual_amount' => $cardDeposit->actual_amount,
                    'serial' => $cardDeposit->serial,
                    'request_id' => $cardDeposit->request_id,
                    'trans_id' => $cardDeposit->trans_id
                ]
            );
            
            // Lưu ID giao dịch vào bản ghi thẻ cào
            $cardDeposit->transaction_id = $transaction->id;
            
            Log::info('TheSieuRe Webhook: Thẻ nạp thành công', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'amount' => $cardDeposit->amount,
                'actual_amount' => $cardDeposit->actual_amount
            ]);
        } elseif ($status == 3) {
            // Thẻ sai mệnh giá
            $theSieuReService = new TheSieuReService();
            
            // Cập nhật số tiền thực tế
            $realAmount = intval($amount);
            $cardDeposit->actual_amount = $theSieuReService->calculateActualAmount($cardDeposit->telco, $realAmount);
            $cardDeposit->status = 'completed';
            $cardDeposit->completed_at = now();
            
            // Lấy ví của người dùng
            $user = User::find($cardDeposit->user_id);
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                    'is_active' => true,
                ]);
            }
            
            // Sử dụng phương thức deposit của ví để nạp tiền và tạo giao dịch
            $transaction = $wallet->deposit(
                $cardDeposit->actual_amount,
                WalletTransaction::TYPE_DEPOSIT,
                'Nạp tiền qua thẻ cào ' . $cardDeposit->getTelcoNameAttribute() . ' (sai mệnh giá, thực tế: ' . number_format($realAmount) . ' VNĐ)',
                $cardDeposit->id,
                'card_deposit',
                [
                    'telco' => $cardDeposit->telco,
                    'amount' => $cardDeposit->amount,
                    'actual_amount' => $cardDeposit->actual_amount,
                    'real_amount' => $realAmount,
                    'serial' => $cardDeposit->serial,
                    'request_id' => $cardDeposit->request_id,
                    'trans_id' => $cardDeposit->trans_id,
                    'wrong_amount' => true
                ]
            );
            
            // Lưu ID giao dịch vào bản ghi thẻ cào
            $cardDeposit->transaction_id = $transaction->id;
            
            Log::info('TheSieuRe Webhook: Thẻ sai mệnh giá nhưng đã nạp thành công', [
                'request_id' => $requestId,
                'user_id' => $user->id,
                'amount' => $cardDeposit->amount,
                'actual_amount' => $cardDeposit->actual_amount,
                'real_amount' => $realAmount
            ]);
        } elseif ($status == 4) {
            // Thẻ không đúng hoặc đã được sử dụng
            $cardDeposit->status = 'failed';
            $cardDeposit->completed_at = now();
            
            Log::info('TheSieuRe Webhook: Thẻ không hợp lệ', [
                'request_id' => $requestId
            ]);
        } else {
            // Các trường hợp khác, không cập nhật trạng thái
            return response()->json([
                'status' => 'success', 
                'message' => 'Đã nhận thông tin nhưng chưa xử lý'
            ]);
        }
        
        $cardDeposit->save();
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Đã xử lý thông tin thẻ cào thành công'
        ]);
    }

    /**
     * Kiểm tra trạng thái nạp tiền qua API
     */
    public function checkDepositStatus(Request $request)
    {
        $request->validate([
            'deposit_code' => 'required|string',
            'amount' => 'required|numeric'
        ]);
        
        $depositCode = $request->input('deposit_code');
        $amount = $request->input('amount');
        
        Log::info('Kiểm tra trạng thái nạp tiền', [
            'deposit_code' => $depositCode,
            'amount' => $amount,
            'user_id' => Auth::id()
        ]);
        
        // Kiểm tra trong bảng wallet_deposits
        try {
            $deposit = DB::table('wallet_deposits')
                ->where('deposit_code', $depositCode)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($deposit && $deposit->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'message' => 'Thanh toán đã được xác nhận thành công!'
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Lỗi khi truy vấn bảng wallet_deposits', [
                'deposit_code' => $depositCode,
                'error' => $e->getMessage()
            ]);
        }
        
        // Xem trong database đã có giao dịch nạp tiền thành công chưa
        $transaction = WalletTransaction::where('metadata', 'like', '%' . $depositCode . '%')
            ->where('user_id', Auth::id())
            ->where('type', WalletTransaction::TYPE_DEPOSIT)
            ->first();
        
        if ($transaction) {
            // Đã có giao dịch thành công
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'message' => 'Thanh toán đã được xác nhận thành công!'
            ]);
        }
        
        // Kiểm tra trạng thái từ SePay
        $sePayService = new SePayService();
        $status = $sePayService->checkTransactionStatus($depositCode);
        
        return response()->json($status);
    }
}
