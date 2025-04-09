<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\BoostingOrder;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
                // WALLET là prefix của đơn nạp ví
                $isWalletDeposit = strpos($content, 'WALLET') !== false;
                
                // Trích xuất order_number từ nội dung
                if ($isBoostingOrder) {
                    // Tìm vị trí của "ORDBOOST" trong chuỗi
                    $ordBoostPos = strpos($content, 'ORDBOOST');
                    if ($ordBoostPos !== false) {
                        // Lấy phần sau 'ORDBOOST'
                        $orderNumber = substr($content, $ordBoostPos + 8); // 8 là độ dài của 'ORDBOOST'
                        
                        // Loại bỏ các ký tự không phải số
                        $orderNumber = preg_replace('/[^0-9]/', '', $orderNumber);
                        
                        // Thêm lại prefix để có mã đơn hàng đầy đủ
                        $orderNumber = 'BOOST' . $orderNumber;
                        
                        Log::info('SePay Webhook API: Tìm thấy đơn hàng cày thuê', [
                            'order_number' => $orderNumber
                        ]);
                    } else {
                        Log::warning('SePay Webhook API: Không thể trích xuất mã đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['success' => true]);
                    }
                } else if ($isWalletDeposit) {
                    // Tìm vị trí của "ORDWALLET" trong chuỗi
                    $ordWalletPos = strpos($content, 'ORDWALLET');
                    if ($ordWalletPos !== false) {
                        // Lấy phần sau 'ORDWALLET'
                        $walletCode = substr($content, $ordWalletPos + 9); // 9 là độ dài của 'ORDWALLET'
                        
                        // Loại bỏ các ký tự không phải số
                        $walletCode = preg_replace('/[^0-9]/', '', $walletCode);
                        
                        // Thêm lại prefix để có mã đơn hàng đầy đủ
                        $depositCode = 'WALLET-' . $walletCode;
                        
                        Log::info('SePay Webhook API: Tìm thấy yêu cầu nạp ví', [
                            'deposit_code' => $depositCode,
                            'wallet_code' => $walletCode
                        ]);
                        
                        // Tìm người dùng sở hữu mã nạp tiền này - Từ session
                        $sessions = DB::table('sessions')
                            ->get();
                            
                        $userId = null;
                        
                        foreach ($sessions as $session) {
                            $sessionData = @unserialize(base64_decode($session->payload));
                            if ($sessionData && 
                                isset($sessionData['deposit']) && 
                                isset($sessionData['deposit']['code']) && 
                                $sessionData['deposit']['code'] === $depositCode) {
                                
                                $userId = $session->user_id;
                                Log::info('SePay Webhook API: Tìm thấy session nạp ví', [
                                    'deposit_code' => $depositCode,
                                    'user_id' => $userId
                                ]);
                                break;
                            }
                        }
                        
                        if ($userId) {
                            // Nếu tìm thấy người dùng, xử lý nạp tiền vào ví
                            $amount = (int)$data['transferAmount'];
                            
                            // Gọi phương thức xử lý nạp tiền
                            $result = \App\Http\Controllers\WalletController::processDepositWebhook(
                                $userId, 
                                $amount, 
                                $data, 
                                $depositCode
                            );
                            
                            if ($result) {
                                Log::info('SePay Webhook API: Đã nạp tiền vào ví thành công', [
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'deposit_code' => $depositCode
                                ]);
                            } else {
                                Log::error('SePay Webhook API: Lỗi khi nạp tiền vào ví', [
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'deposit_code' => $depositCode
                                ]);
                            }
                        } else {
                            Log::warning('SePay Webhook API: Không tìm thấy người dùng cho mã nạp tiền', [
                                'deposit_code' => $depositCode
                            ]);
                        }
                        
                        return response()->json(['success' => true]);
                    } else {
                        Log::warning('SePay Webhook API: Không tìm thấy đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['success' => true]);
                    }
                } else if (strpos($content, 'ORDPRODUCT') !== false) {
                    // Đơn hàng thường
                    $ordProductPos = strpos($content, 'ORDPRODUCT');
                    if ($ordProductPos !== false) {
                        // Lấy phần sau 'ORDPRODUCT'
                        $cleanedOrderNumber = substr($content, $ordProductPos + 10); // 10 là độ dài của 'ORDPRODUCT'
                        
                        // Loại bỏ các ký tự không phải số và chữ cái
                        $cleanedOrderNumber = preg_replace('/[^0-9A-Z]/', '', $cleanedOrderNumber);
                        
                        // Thêm lại prefix ORD- để có mã đơn hàng đầy đủ
                        $fullOrderNumber = 'ORD-' . $cleanedOrderNumber;
                        
                        Log::info('SePay Webhook API: Đã tách mã đơn hàng thường', [
                            'original_content' => $content,
                            'cleaned_order_number' => $cleanedOrderNumber,
                            'full_order_number' => $fullOrderNumber
                        ]);
                        
                        // Tìm đơn hàng thường - phương pháp 1: tìm chính xác
                        $order = Order::where('order_number', $fullOrderNumber)
                            ->where('status', 'pending')
                            ->first();
                    
                        if (!$order) {
                            // Phương pháp 2: Tìm kiếm không có ký tự dash
                            $altOrderNumber = str_replace('-', '', $fullOrderNumber); 
                            $order = Order::where('order_number', 'like', '%' . $altOrderNumber . '%')
                                ->where('status', 'pending')
                                ->first();
                                
                            // Phương pháp 3: Tìm kiếm chỉ với phần số
                            if (!$order) {
                                Log::info('SePay Webhook API: Thử tìm kiếm mở rộng với LIKE', [
                                    'cleaned_order_number' => $cleanedOrderNumber
                                ]);
                                $order = Order::where('order_number', 'like', '%' . $cleanedOrderNumber . '%')
                                    ->where('status', 'pending')
                                    ->first();
                            }
                            
                            // Phương pháp 4: Tìm kiếm với toàn bộ order_number
                            if (!$order) {
                                Log::info('SePay Webhook API: Thử tìm tất cả đơn hàng thường có status pending');
                                $pendingOrders = Order::where('status', 'pending')->get();
                                foreach ($pendingOrders as $pendingOrder) {
                                    $orderNumberWithoutPrefix = str_replace('ORD-', '', $pendingOrder->order_number);
                                    if (strpos($orderNumberWithoutPrefix, $cleanedOrderNumber) !== false) {
                                        $order = $pendingOrder;
                                        Log::info('SePay Webhook API: Tìm thấy đơn hàng với phương pháp 4', [
                                            'order_number' => $pendingOrder->order_number
                                        ]);
                                        break;
                                    }
                                }
                            }
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
                                'order_number' => $fullOrderNumber
                            ]);
                        }
                    } else {
                        Log::warning('SePay Webhook API: Không thể trích xuất mã đơn hàng thường', [
                            'content' => $content
                        ]);
                        return response()->json(['success' => true]);
                    }
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
                // WALLET là prefix của đơn nạp ví
                $isWalletDeposit = strpos($content, 'WALLET') !== false;
                
                // Trích xuất order_number từ nội dung
                if ($isBoostingOrder) {
                    // Tìm vị trí của "ORDBOOST" trong chuỗi
                    $ordBoostPos = strpos($content, 'ORDBOOST');
                    if ($ordBoostPos !== false) {
                        // Lấy phần sau 'ORDBOOST'
                        $orderNumber = substr($content, $ordBoostPos + 8); // 8 là độ dài của 'ORDBOOST'
                        
                        // Loại bỏ các ký tự không phải số
                        $orderNumber = preg_replace('/[^0-9]/', '', $orderNumber);
                        
                        // Thêm lại prefix để có mã đơn hàng đầy đủ
                        $orderNumber = 'BOOST' . $orderNumber;
                        
                        Log::info('SePay Webhook: Tìm thấy đơn hàng cày thuê', [
                            'order_number' => $orderNumber
                        ]);
                    } else {
                        Log::warning('SePay Webhook: Không thể trích xuất mã đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Không thể trích xuất mã đơn hàng']);
                    }
                } else if ($isWalletDeposit) {
                    // Tìm vị trí của "ORDWALLET" trong chuỗi
                    $ordWalletPos = strpos($content, 'ORDWALLET');
                    if ($ordWalletPos !== false) {
                        // Lấy phần sau 'ORDWALLET'
                        $walletCode = substr($content, $ordWalletPos + 9); // 9 là độ dài của 'ORDWALLET'
                        
                        // Loại bỏ các ký tự không phải số
                        $walletCode = preg_replace('/[^0-9]/', '', $walletCode);
                        
                        // Thêm lại prefix để có mã đơn hàng đầy đủ
                        $depositCode = 'WALLET-' . $walletCode;
                        
                        Log::info('SePay Webhook: Tìm thấy yêu cầu nạp ví', [
                            'deposit_code' => $depositCode,
                            'wallet_code' => $walletCode
                        ]);
                        
                        // Tìm người dùng sở hữu mã nạp tiền này - Từ session
                        $sessions = DB::table('sessions')
                            ->get();
                            
                        $userId = null;
                        
                        foreach ($sessions as $session) {
                            $sessionData = @unserialize(base64_decode($session->payload));
                            if ($sessionData && 
                                isset($sessionData['deposit']) && 
                                isset($sessionData['deposit']['code']) && 
                                $sessionData['deposit']['code'] === $depositCode) {
                                
                                $userId = $session->user_id;
                                Log::info('SePay Webhook: Tìm thấy session nạp ví', [
                                    'deposit_code' => $depositCode,
                                    'user_id' => $userId
                                ]);
                                break;
                            }
                        }
                        
                        if ($userId) {
                            // Nếu tìm thấy người dùng, xử lý nạp tiền vào ví
                            $amount = (int)$data['transferAmount'];
                            
                            // Gọi phương thức xử lý nạp tiền
                            $result = \App\Http\Controllers\WalletController::processDepositWebhook(
                                $userId, 
                                $amount, 
                                $data, 
                                $depositCode
                            );
                            
                            if ($result) {
                                Log::info('SePay Webhook: Đã nạp tiền vào ví thành công', [
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'deposit_code' => $depositCode
                                ]);
                            } else {
                                Log::error('SePay Webhook: Lỗi khi nạp tiền vào ví', [
                                    'user_id' => $userId,
                                    'amount' => $amount,
                                    'deposit_code' => $depositCode
                                ]);
                            }
                        } else {
                            Log::warning('SePay Webhook: Không tìm thấy người dùng cho mã nạp tiền', [
                                'deposit_code' => $depositCode
                            ]);
                        }
                        
                        return response()->json(['success' => true]);
                    } else {
                        Log::warning('SePay Webhook: Không tìm thấy đơn hàng cày thuê', [
                            'content' => $content
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng cày thuê']);
                    }
                } else if (strpos($content, 'ORDPRODUCT') !== false) {
                    // Đơn hàng thường
                    $ordProductPos = strpos($content, 'ORDPRODUCT');
                    if ($ordProductPos !== false) {
                        // Lấy phần sau 'ORDPRODUCT'
                        $cleanedOrderNumber = substr($content, $ordProductPos + 10); // 10 là độ dài của 'ORDPRODUCT'
                        
                        // Loại bỏ các ký tự không phải số và chữ cái
                        $cleanedOrderNumber = preg_replace('/[^0-9A-Z]/', '', $cleanedOrderNumber);
                        
                        // Thêm lại prefix ORD- để có mã đơn hàng đầy đủ
                        $fullOrderNumber = 'ORD-' . $cleanedOrderNumber;
                        
                        Log::info('SePay Webhook: Đã tách mã đơn hàng thường', [
                            'original_content' => $content,
                            'cleaned_order_number' => $cleanedOrderNumber,
                            'full_order_number' => $fullOrderNumber
                        ]);
                        
                        // Tìm đơn hàng thường - phương pháp 1: tìm chính xác
                        $order = Order::where('order_number', $fullOrderNumber)
                            ->where('status', 'pending')
                            ->first();
                    
                        if (!$order) {
                            // Phương pháp 2: Tìm kiếm không có ký tự dash
                            $altOrderNumber = str_replace('-', '', $fullOrderNumber); 
                            $order = Order::where('order_number', 'like', '%' . $altOrderNumber . '%')
                                ->where('status', 'pending')
                                ->first();
                                
                            // Phương pháp 3: Tìm kiếm chỉ với phần số
                            if (!$order) {
                                Log::info('SePay Webhook: Thử tìm kiếm mở rộng với LIKE', [
                                    'cleaned_order_number' => $cleanedOrderNumber
                                ]);
                                $order = Order::where('order_number', 'like', '%' . $cleanedOrderNumber . '%')
                                    ->where('status', 'pending')
                                    ->first();
                            }
                            
                            // Phương pháp 4: Tìm kiếm với toàn bộ order_number
                            if (!$order) {
                                Log::info('SePay Webhook: Thử tìm tất cả đơn hàng thường có status pending');
                                $pendingOrders = Order::where('status', 'pending')->get();
                                foreach ($pendingOrders as $pendingOrder) {
                                    $orderNumberWithoutPrefix = str_replace('ORD-', '', $pendingOrder->order_number);
                                    if (strpos($orderNumberWithoutPrefix, $cleanedOrderNumber) !== false) {
                                        $order = $pendingOrder;
                                        Log::info('SePay Webhook: Tìm thấy đơn hàng với phương pháp 4', [
                                            'order_number' => $pendingOrder->order_number
                                        ]);
                                        break;
                                    }
                                }
                            }
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
                                'order_number' => $fullOrderNumber
                            ]);
                        }
                    } else {
                        Log::warning('SePay Webhook: Không thể trích xuất mã đơn hàng thường', [
                            'content' => $content
                        ]);
                        return response()->json(['success' => true]);
                    }
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
