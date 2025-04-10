<?php

namespace App\Http\Controllers;

use App\Models\CardDeposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\TheSieuReService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookTheSieuReController extends Controller
{
    /**
     * Xử lý callback từ TheSieuRe
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleCallback(Request $request)
    {
        
        // Thử kiểm tra chữ ký bằng nhiều cách khác nhau
        $this->tryDifferentSignatureMethods($request->all());
        
        // Khởi tạo TheSieuRe Service
        $theSieuReService = new TheSieuReService();
        
        // Xác thực dữ liệu callback 
        if (!$theSieuReService->verifyCallback($request->all())) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu callback không hợp lệ'
            ], 400);
        }
        
        
        // Tìm giao dịch nạp thẻ dựa trên request_id
        $cardDeposit = CardDeposit::where('request_id', $request->request_id)->first();
        
        if (!$cardDeposit) {
            
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch nạp thẻ'
            ], 404);
        }
        
        // Kiểm tra trạng thái trước khi xử lý
        if ($cardDeposit->status == CardDeposit::STATUS_COMPLETED) {
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch đã được xử lý thành công trước đó'
            ]);
        }
        
        // Cập nhật thông tin từ callback
        $cardDeposit->trans_id = $request->trans_id;
        $cardDeposit->actual_amount = $request->value;
        $cardDeposit->metadata = array_merge($cardDeposit->metadata ?? [], [
            'callback_data' => $request->all(),
            'callback_time' => now()->format('Y-m-d H:i:s'),
        ]);

        // Xử lý theo trạng thái
        $status = (int)$request->status; // Chuyển đổi sang số nguyên để so sánh chính xác
        
        if ($status == 1) { // Thẻ đúng - gạch thẻ thành công
            // Cập nhật trạng thái thẻ thành công
            $cardDeposit->status = CardDeposit::STATUS_COMPLETED;
            $cardDeposit->completed_at = now();
            
            // Cập nhật giá trị actual_amount từ response nếu chưa có
            if (!$cardDeposit->actual_amount || $cardDeposit->actual_amount == 0) {
                $cardDeposit->actual_amount = $request->amount;
            }
            
            $cardDeposit->save();
            
            // Tìm user để cập nhật ví
            $user = User::find($cardDeposit->user_id);
            if (!$user) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }
            
            // Lấy ví của người dùng
            $wallet = $user->getWallet();
            
            // Cập nhật số dư ví với số tiền AMOUNT (đã trừ phí) 
            // QUAN TRỌNG: Chỉ cộng amount (số tiền thực nhận), không cộng value (mệnh giá thẻ)
            $wallet->balance += $request->amount;
            $wallet->save();
            
            // Bắt đầu transaction để đảm bảo tính nhất quán dữ liệu
            DB::beginTransaction();
            
            try {
                // Tạo lịch sử giao dịch
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'type' => 'deposit',
                    'status' => 'completed',
                    'payment_method' => 'card',
                    'description' => 'Nạp thẻ ' . $request->telco . ' mệnh giá ' . number_format($request->declared_value) . 'đ',
                    'metadata' => [
                        'card_deposit_id' => $cardDeposit->id,
                        'real_amount' => $request->amount,
                        'declared_amount' => $request->declared_value,
                        'telco' => $request->telco,
                        'serial' => $request->serial,
                        'trans_id' => $request->trans_id,
                    ]
                ]);
                
                // Tạo thêm bản ghi trong wallet_transactions
                $walletTransaction = WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'balance_before' => $wallet->balance - $request->amount,
                    'balance_after' => $wallet->balance,
                    'type' => WalletTransaction::TYPE_DEPOSIT,
                    'description' => 'Nạp thẻ ' . $request->telco . ' mệnh giá ' . number_format($request->declared_value) . 'đ',
                    'reference_id' => $cardDeposit->id,
                    'reference_type' => 'card_deposit',
                    'metadata' => json_encode([
                        'card_deposit_id' => $cardDeposit->id,
                        'transaction_id' => $transaction->id,
                        'telco' => $request->telco,
                        'serial' => $request->serial,
                        'code' => $request->code,
                        'amount' => $request->amount,
                        'declared_value' => $request->declared_value
                    ])
                ]);
                
                DB::commit();
                
          
            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('TheSieuRe: Lỗi khi tạo giao dịch', [
                    'user_id' => $user->id,
                    'card_deposit_id' => $cardDeposit->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật giao dịch: ' . $e->getMessage()
                ], 500);
            }
            
            // Gửi thông báo cho người dùng (nếu cần)
            // Todo: Implement notification logic
        } else if ($status == 2) { // Thẻ đúng - đang chờ xử lý
            // Cập nhật trạng thái thẻ đang chờ xử lý
            $cardDeposit->status = CardDeposit::STATUS_PENDING;
            $cardDeposit->save();
          
        } else if ($status == 3) { // Thẻ sai mệnh giá
            // Đánh dấu thẻ thất bại với lý do cụ thể
            $cardDeposit->status = CardDeposit::STATUS_FAILED;
            $cardDeposit->metadata = array_merge($cardDeposit->metadata ?? [], [
                'failure_reason' => 'Thẻ sai mệnh giá',
                'expected_value' => $request->declared_value,
                'actual_value' => $request->value,
            ]);
            $cardDeposit->save();
            
        } else if ($status == 4) { // Thẻ không đúng hoặc đã sử dụng
            // Đánh dấu thẻ thất bại
            $cardDeposit->status = CardDeposit::STATUS_FAILED;
            $cardDeposit->metadata = array_merge($cardDeposit->metadata ?? [], [
                'failure_reason' => 'Thẻ không đúng hoặc đã được sử dụng'
            ]);
            $cardDeposit->save();
          
        } else { // Các trạng thái khác
            // Đánh dấu thẻ thất bại
            $cardDeposit->status = CardDeposit::STATUS_FAILED;
            $cardDeposit->metadata = array_merge($cardDeposit->metadata ?? [], [
                'failure_reason' => 'Lỗi không xác định, mã trạng thái: ' . $status,
                'raw_data' => $request->all()
            ]);
            $cardDeposit->save();
       
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Callback đã được xử lý thành công'
        ]);
    }

    /**
     * Thử nhiều phương thức khác nhau để tạo chữ ký và kiểm tra
     * 
     * @param array $data Dữ liệu từ callback
     * @return void
     */
    private function tryDifferentSignatureMethods($data)
    {
        // Lấy partner key từ config
        $partnerKey = config('services.thesieure.partner_key');
        
        // Thử các phương thức khác nhau
        if (isset($data['code']) && isset($data['serial']) && isset($data['status']) && isset($data['callback_sign'])) {
            $receivedSign = $data['callback_sign'];
            
            // Phương thức 1: key + code + serial + status
            $string1 = $partnerKey . $data['code'] . $data['serial'] . $data['status'];
            $sign1 = md5($string1);
            
            // Phương thức 2: key + code + serial
            $string2 = $partnerKey . $data['code'] . $data['serial'];
            $sign2 = md5($string2);
            
            // Phương thức 3: code + serial + key
            $string3 = $data['code'] . $data['serial'] . $partnerKey;
            $sign3 = md5($string3);
            
            // Phương thức 4: key + status + code + serial
            $string4 = $partnerKey . $data['status'] . $data['code'] . $data['serial'];
            $sign4 = md5($string4);
            
            // Phương thức 5: key + amount + code + serial
            $string5 = $partnerKey . $data['amount'] . $data['code'] . $data['serial'];
            $sign5 = md5($string5);
            
        }
    }
}
