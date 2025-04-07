<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\BoostingOrder;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Xử lý webhook từ SePay (version mới, giống phương thức sepayWebhook)
     */
    public function sepay(Request $request)
    {
        // Đọc dữ liệu từ request
        $data = $request->all();
        
        // Log toàn bộ dữ liệu nhận được để debug
        Log::info('SePay Webhook API: Nhận dữ liệu', $data);
        
        // Xác thực chữ ký (bỏ qua trong môi trường phát triển)
        // TODO: Thêm xác thực chữ ký khi triển khai lên production
        
        // Kiểm tra loại giao dịch
        if (!isset($data['transferType'])) {
            Log::error('SePay Webhook API: Thiếu thông tin loại giao dịch');
            return response()->json(['status' => 'error', 'message' => 'Thiếu thông tin giao dịch']);
        }
        
        if ($data['transferType'] === 'in') {
            // Tìm đơn hàng dựa trên mã nội dung chuyển khoản
            $pattern = config('payment.pattern', 'SEVQR');
            $content = $data['content'];
            
            Log::info('SePay Webhook API: Xử lý nội dung', [
                'pattern' => $pattern,
                'content' => $content
            ]);
            
            // Kiểm tra xem nội dung có chứa pattern không
            if (strpos($content, $pattern) !== false) {
                // BOOST là prefix của đơn hàng cày thuê
                $isBoostingOrder = strpos($content, 'BOOST') !== false;
                
                // Trích xuất order_number từ nội dung
                if ($isBoostingOrder) {
                    // Tìm vị trí của "BOOST" trong chuỗi
                    $boostPos = strpos($content, 'BOOST');
                    if ($boostPos !== false) {
                        // Lấy phần sau 'BOOST'
                        $rawOrderNumber = substr($content, $boostPos);
                        // Loại bỏ các ký tự không phải chữ và số
                        $orderNumber = preg_replace('/[^a-zA-Z0-9]/', '', $rawOrderNumber);
                        
                        Log::info('SePay Webhook API: Tìm thấy đơn hàng cày thuê', [
                            'raw_order_number' => $rawOrderNumber,
                            'cleaned_order_number' => $orderNumber
                        ]);
                    } else {
                        Log::warning('SePay Webhook API: Không thể trích xuất mã đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['success' => true]);
                    }
                } else if (strpos($content, 'ORD') !== false) {
                    // Đơn hàng thường
                    $orderParts = explode('ORD', $content);
                    // Lấy phần sau 'ORD'
                    $rawOrderNumber = trim(end($orderParts));
                    
                    // Loại bỏ các ký tự không phải chữ và số, nhưng giữ lại dấu gạch ngang
                    $orderNumber = preg_replace('/[^a-zA-Z0-9\-]/', '', $rawOrderNumber);
                    
                    // Kiểm tra nếu mã đơn hàng bắt đầu bằng dấu gạch ngang, loại bỏ nó
                    if (strpos($orderNumber, '-') === 0) {
                        $orderNumber = substr($orderNumber, 1);
                    }
                    
                    Log::info('SePay Webhook API: Đã tách mã đơn hàng thường', [
                        'original_content' => $content,
                        'raw_order_number' => $rawOrderNumber,
                        'cleaned_order_number' => $orderNumber
                    ]);
                } else {
                    Log::warning('SePay Webhook API: Không tìm thấy định dạng đơn hàng hợp lệ', [
                        'content' => $content
                    ]);
                    return response()->json(['success' => true]);
                }
                
                // Tìm đơn hàng phù hợp (cày thuê hoặc thường)
                if ($isBoostingOrder) {
                    $order = BoostingOrder::where('order_number', 'like', '%' . $orderNumber . '%')
                        ->where('status', 'pending')
                        ->first();
                    
                    if ($order) {
                        Log::info('SePay Webhook API: Đã tìm thấy đơn hàng cày thuê', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $order->status
                        ]);
                        
                        // Kiểm tra số tiền
                        if ((int)$order->amount !== (int)$data['transferAmount']) {
                            Log::warning('SePay Webhook API: Số tiền không khớp cho đơn hàng cày thuê', [
                                'order_id' => $order->id,
                                'expected' => $order->amount,
                                'received' => $data['transferAmount'],
                            ]);
                        }
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'paid';
                        $order->save();
                        
                        Log::info('SePay Webhook API: Đã cập nhật đơn hàng cày thuê thành paid', [
                            'order_id' => $order->id
                        ]);
                        
                        // Lưu giao dịch
                        Transaction::create([
                            'order_id' => null, // Đơn hàng cày thuê không liên kết với bảng orders
                            'boosting_order_id' => $order->id,
                            'amount' => $data['transferAmount'],
                            'payment_method' => 'bank_transfer',
                            'transaction_id' => $data['id'] ?? null,
                            'status' => 'completed',
                            'notes' => json_encode($data)
                        ]);
                        
                        return response()->json(['success' => true]);
                    } else {
                        Log::warning('SePay Webhook API: Không tìm thấy đơn hàng cày thuê', [
                            'order_number' => $orderNumber
                        ]);
                    }
                } else {
                    // Đơn hàng thường
                    $order = Order::where('order_number', $orderNumber)
                        ->where('status', 'pending')
                        ->first();
                
                    if (!$order) {
                        Log::info('SePay Webhook API: Thử tìm kiếm với LIKE', ['pattern' => $orderNumber]);
                        $order = Order::where('order_number', 'like', '%' . $orderNumber . '%')
                            ->where('status', 'pending')
                            ->first();
                    }
                    
                    if ($order) {
                        Log::info('SePay Webhook API: Đã tìm thấy đơn hàng thường', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $order->status
                        ]);
                        
                        // Kiểm tra số tiền
                        if ((int)$order->amount !== (int)$data['transferAmount']) {
                            Log::warning('SePay Webhook API: Số tiền không khớp cho đơn hàng thường', [
                                'order_id' => $order->id,
                                'expected' => $order->amount,
                                'received' => $data['transferAmount'],
                            ]);
                        }
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'completed';
                        $order->completed_at = Carbon::now();
                        $order->save();
                        
                        Log::info('SePay Webhook API: Đã cập nhật đơn hàng thường thành completed', [
                            'order_id' => $order->id
                        ]);
                        
                        // Cập nhật trạng thái tài khoản
                        if ($order->account) {
                            $order->account->status = 'sold';
                            $order->account->save();
                            
                            Log::info('SePay Webhook API: Đã cập nhật trạng thái tài khoản thành sold', [
                                'account_id' => $order->account->id
                            ]);
                        }
                        
                        // Lưu giao dịch
                        Transaction::create([
                            'order_id' => $order->id,
                            'boosting_order_id' => null,
                            'amount' => $data['transferAmount'],
                            'payment_method' => 'bank_transfer',
                            'transaction_id' => $data['id'] ?? null,
                            'status' => 'completed',
                            'notes' => json_encode($data)
                        ]);
                        
                        return response()->json(['success' => true]);
                    } else {
                        Log::warning('SePay Webhook API: Không tìm thấy đơn hàng thường', [
                            'order_number' => $orderNumber
                        ]);
                    }
                }
            }
            
            // Không tìm thấy đơn hàng phù hợp, nhưng vẫn trả về thành công để SePay không gửi lại
            Log::warning('SePay Webhook API: Không tìm thấy đơn hàng phù hợp', [
                'content' => $content
            ]);
        }
        
        // Luôn trả về thành công để SePay không gửi lại webhook
        return response()->json(['success' => true]);
    }
    
    /**
     * Xử lý webhook từ SePay
     */
    public function sepayWebhook(Request $request)
    {
        // Đọc dữ liệu từ request
        $data = $request->all();
        
        // Log toàn bộ dữ liệu nhận được để debug
        Log::info('SePay Webhook: Nhận dữ liệu', $data);
        
        // Xác thực chữ ký (bỏ qua trong môi trường phát triển)
        // TODO: Thêm xác thực chữ ký khi triển khai lên production
        
        // Kiểm tra loại giao dịch
        if (!isset($data['transferType'])) {
            Log::error('SePay Webhook: Thiếu thông tin loại giao dịch');
            return response()->json(['status' => 'error', 'message' => 'Thiếu thông tin giao dịch']);
        }
        
        if ($data['transferType'] === 'in') {
            // Tìm đơn hàng dựa trên mã nội dung chuyển khoản
            $pattern = config('payment.pattern', 'SEVQR');
            $content = $data['content'];
            
            Log::info('SePay Webhook: Xử lý nội dung', [
                'pattern' => $pattern,
                'content' => $content
            ]);
            
            // Kiểm tra xem nội dung có chứa pattern không
            if (strpos($content, $pattern) !== false) {
                // BOOST là prefix của đơn hàng cày thuê
                $isBoostingOrder = strpos($content, 'BOOST') !== false;
                
                // Trích xuất order_number từ nội dung
                if ($isBoostingOrder) {
                    // Tìm vị trí của "BOOST" trong chuỗi
                    $boostPos = strpos($content, 'BOOST');
                    if ($boostPos !== false) {
                        // Lấy phần sau 'BOOST'
                        $rawOrderNumber = substr($content, $boostPos);
                        // Loại bỏ các ký tự không phải chữ và số
                        $orderNumber = preg_replace('/[^a-zA-Z0-9]/', '', $rawOrderNumber);
                        
                        Log::info('SePay Webhook: Tìm thấy đơn hàng cày thuê', [
                            'raw_order_number' => $rawOrderNumber,
                            'cleaned_order_number' => $orderNumber
                        ]);
                    } else {
                        Log::warning('SePay Webhook: Không thể trích xuất mã đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Không thể trích xuất mã đơn hàng']);
                    }
                } else if (strpos($content, 'ORD') !== false) {
                    // Đơn hàng thường
                    $orderParts = explode('ORD', $content);
                    // Lấy phần sau 'ORD'
                    $rawOrderNumber = trim(end($orderParts));
                    
                    // Loại bỏ các ký tự không phải chữ và số, nhưng giữ lại dấu gạch ngang
                    $orderNumber = preg_replace('/[^a-zA-Z0-9\-]/', '', $rawOrderNumber);
                    
                    // Kiểm tra nếu mã đơn hàng bắt đầu bằng dấu gạch ngang, loại bỏ nó
                    if (strpos($orderNumber, '-') === 0) {
                        $orderNumber = substr($orderNumber, 1);
                    }
                    
                    Log::info('SePay Webhook: Đã tách mã đơn hàng thường', [
                        'original_content' => $content,
                        'raw_order_number' => $rawOrderNumber,
                        'cleaned_order_number' => $orderNumber
                    ]);
                } else {
                    Log::warning('SePay Webhook: Không tìm thấy định dạng đơn hàng hợp lệ', [
                        'content' => $content
                    ]);
                    return response()->json(['status' => 'error', 'message' => 'Không tìm thấy định dạng đơn hàng']);
                }
                
                // Tìm đơn hàng phù hợp (cày thuê hoặc thường)
                if ($isBoostingOrder) {
                    $order = BoostingOrder::where('order_number', 'like', '%' . $orderNumber . '%')
                        ->where('status', 'pending')
                        ->first();
                    
                    if ($order) {
                        Log::info('SePay Webhook: Đã tìm thấy đơn hàng cày thuê', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $order->status
                        ]);
                        
                        // Kiểm tra số tiền
                        if ((int)$order->amount !== (int)$data['transferAmount']) {
                            Log::warning('SePay Webhook: Số tiền không khớp cho đơn hàng cày thuê', [
                                'order_id' => $order->id,
                                'expected' => $order->amount,
                                'received' => $data['transferAmount'],
                            ]);
                        }
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'paid';
                        $order->save();
                        
                        Log::info('SePay Webhook: Đã cập nhật đơn hàng cày thuê thành paid', [
                            'order_id' => $order->id
                        ]);
                        
                        // Lưu giao dịch
                        Transaction::create([
                            'order_id' => null, // Đơn hàng cày thuê không liên kết với bảng orders
                            'boosting_order_id' => $order->id,
                            'amount' => $data['transferAmount'],
                            'payment_method' => 'bank_transfer',
                            'transaction_id' => $data['id'] ?? null,
                            'status' => 'completed',
                            'notes' => json_encode($data)
                        ]);
                        
                        return response()->json(['status' => 'success', 'message' => 'Đã cập nhật đơn hàng cày thuê']);
                    } else {
                        Log::warning('SePay Webhook: Không tìm thấy đơn hàng cày thuê', [
                            'order_number' => $orderNumber
                        ]);
                    }
                } else {
                    // Đơn hàng thường
                    $order = Order::where('order_number', $orderNumber)
                        ->where('status', 'pending')
                        ->first();
                
                    if (!$order) {
                        Log::info('SePay Webhook: Thử tìm kiếm với LIKE', ['pattern' => $orderNumber]);
                        $order = Order::where('order_number', 'like', '%' . $orderNumber . '%')
                            ->where('status', 'pending')
                            ->first();
                    }
                    
                    if ($order) {
                        Log::info('SePay Webhook: Đã tìm thấy đơn hàng thường', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $order->status
                        ]);
                        
                        // Kiểm tra số tiền
                        if ((int)$order->amount !== (int)$data['transferAmount']) {
                            Log::warning('SePay Webhook: Số tiền không khớp cho đơn hàng thường', [
                                'order_id' => $order->id,
                                'expected' => $order->amount,
                                'received' => $data['transferAmount'],
                            ]);
                        }
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'completed';
                        $order->completed_at = Carbon::now();
                        $order->save();
                        
                        Log::info('SePay Webhook: Đã cập nhật đơn hàng thường thành completed', [
                            'order_id' => $order->id
                        ]);
                        
                        // Cập nhật trạng thái tài khoản
                        if ($order->account) {
                            $order->account->status = 'sold';
                            $order->account->save();
                            
                            Log::info('SePay Webhook: Đã cập nhật trạng thái tài khoản thành sold', [
                                'account_id' => $order->account->id
                            ]);
                        }
                        
                        // Lưu giao dịch
                        Transaction::create([
                            'order_id' => $order->id,
                            'boosting_order_id' => null,
                            'amount' => $data['transferAmount'],
                            'payment_method' => 'bank_transfer',
                            'transaction_id' => $data['id'] ?? null,
                            'status' => 'completed',
                            'notes' => json_encode($data)
                        ]);
                        
                        return response()->json(['status' => 'success', 'message' => 'Đã cập nhật đơn hàng thường']);
                    } else {
                        Log::warning('SePay Webhook: Không tìm thấy đơn hàng thường', [
                            'order_number' => $orderNumber
                        ]);
                    }
                }
            }
            
            // Không tìm thấy đơn hàng phù hợp
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng phù hợp']);
        }
        
        return response()->json(['status' => 'ignored', 'message' => 'Không phải giao dịch cần xử lý']);
    }
}
