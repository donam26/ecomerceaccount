<?php

namespace App\Http\Controllers;

use App\Models\CardDeposit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TheSieuReService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Log thông tin callback để kiểm tra
        Log::info('TheSieuRe Callback Received', ['data' => $request->all()]);
        
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
            Log::error('TheSieuRe Callback Error: Không tìm thấy giao dịch nạp thẻ', [
                'request_id' => $request->request_id
            ]);
            
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
        $cardDeposit->trans_id = $request->trans_id; // Sửa thành trans_id thay vì transaction_id
        $cardDeposit->actual_amount = $request->value;
        $cardDeposit->metadata = array_merge($cardDeposit->metadata ?? [], [
            'callback_data' => $request->all(),
            'callback_time' => now()->format('Y-m-d H:i:s'),
        ]);

        // Xử lý theo trạng thái
        if ($request->status == CardDeposit::STATUS_COMPLETED) {
            // Cập nhật trạng thái thẻ thành công
            $cardDeposit->status = CardDeposit::STATUS_COMPLETED;
            $cardDeposit->completed_at = now();
            $cardDeposit->save();
            
            // Tìm user để cập nhật ví
            $user = User::find($cardDeposit->user_id);
            if (!$user) {
                Log::error('TheSieuRe Callback Error: Không tìm thấy người dùng', [
                    'user_id' => $cardDeposit->user_id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }
            
            // Lấy ví của người dùng
            $wallet = $user->getWallet();
            
            // Cập nhật số dư ví
            $wallet->balance += $request->value;
            $wallet->save();
            
            // Tạo lịch sử giao dịch
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->value,
                'type' => 'deposit',
                'status' => 'completed',
                'description' => 'Nạp thẻ ' . $request->telco . ' mệnh giá ' . number_format($request->declared_value) . 'đ',
                'metadata' => [
                    'card_deposit_id' => $cardDeposit->id,
                    'real_amount' => $request->value,
                    'declared_amount' => $request->declared_value,
                    'telco' => $request->telco,
                    'serial' => $request->serial,
                    'trans_id' => $request->trans_id,
                ]
            ]);
            
            // Gửi thông báo cho người dùng (nếu cần)
            // Todo: Implement notification logic
        } else if ($request->status == CardDeposit::STATUS_FAILED) {
            // Cập nhật trạng thái thẻ thất bại
            $cardDeposit->markAsFailed();
        } else {
            // Cập nhật trạng thái hiện tại từ callback
            $cardDeposit->status = $request->status;
            $cardDeposit->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Callback đã được xử lý thành công'
        ]);
    }
}
