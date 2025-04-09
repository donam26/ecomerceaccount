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
        Log::info('SePay Webhook nhận được dữ liệu:', $data);
        
        // Kiểm tra loại giao dịch
        if (!isset($data['transferType'])) {
            Log::error('SePay Webhook API: Thiếu thông tin loại giao dịch');
            return response()->json(['status' => 'error', 'message' => 'Thiếu thông tin giao dịch']);
        }
        
        if ($data['transferType'] === 'in') {
            // Tìm đơn hàng dựa trên mã nội dung chuyển khoản
            $pattern = config('payment.pattern', 'SEVQR');
            $content = $data['content'];
            
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
                    } else {
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
                        
                        // Tìm bản ghi trong database
                        $deposit = DB::table('wallet_deposits')
                            ->where('deposit_code', $depositCode)
                            ->where('status', 'pending')
                            ->first();
                        
                        if ($deposit) {
                            Log::info('Tìm thấy thông tin giao dịch trong database:', [
                                'user_id' => $deposit->user_id,
                                'wallet_id' => $deposit->wallet_id,
                                'deposit_code' => $depositCode
                            ]);
                            
                            // Nếu tìm thấy trong database, ưu tiên sử dụng thông tin này
                            $userId = $deposit->user_id;
                            $amount = (int)$data['transferAmount'];
                            
                            // Cập nhật trạng thái deposit
                            DB::table('wallet_deposits')
                                ->where('id', $deposit->id)
                                ->update([
                                    'status' => 'completed',
                                    'response' => json_encode($data),
                                    'completed_at' => now()
                                ]);
                            
                            // Gọi phương thức xử lý nạp tiền
                            $result = \App\Http\Controllers\WalletController::processDepositWebhook(
                                $userId, 
                                $amount, 
                                $data, 
                                $depositCode
                            );
                            
                            return response()->json(['success' => true, 'message' => 'Giao dịch đã được xử lý']);
                        } else {
                            // Nếu không tìm thấy trong database, tìm trong logs
                            $logFiles = glob(storage_path('logs/laravel*.log'));
                            $userId = null;
                            $walletId = null;
                            $expectedAmount = null;
                            
                            foreach ($logFiles as $logFile) {
                                $content = file_get_contents($logFile);
                                
                                // Tìm dòng log chứa mã giao dịch và thông tin nạp ví
                                if (strpos($content, '"deposit_code":"' . $depositCode . '"') !== false && 
                                    strpos($content, 'SePay QR Nạp Ví') !== false) {
                                    
                                    // Tìm user_id
                                    if (preg_match('/"user_id":(\d+)/', $content, $userMatches)) {
                                        $userId = $userMatches[1];
                                        
                                        // Tìm thêm wallet_id nếu có
                                        preg_match('/"wallet_id":(\d+)/', $content, $walletMatches);
                                        if (!empty($walletMatches[1])) {
                                            $walletId = $walletMatches[1];
                                        }
                                        
                                        // Tìm amount
                                        preg_match('/"amount":"?(\d+)"?/', $content, $amountMatches);
                                        if (!empty($amountMatches[1])) {
                                            $expectedAmount = $amountMatches[1];
                                        }
                                        
                                        Log::info('Tìm thấy thông tin giao dịch trong logs với method mới:', [
                                            'user_id' => $userId,
                                            'wallet_id' => $walletId,
                                            'deposit_code' => $depositCode,
                                            'expected_amount' => $expectedAmount
                                        ]);
                                        
                                        break;
                                    }
                                }
                            }
                            
                            if ($userId) {
                                Log::info('Tìm thấy thông tin giao dịch trong logs:', [
                                    'user_id' => $userId,
                                    'wallet_id' => $walletId,
                                    'deposit_code' => $depositCode,
                                    'expected_amount' => $expectedAmount
                                ]);
                                
                                // Nếu tìm thấy người dùng, xử lý nạp tiền vào ví
                                $amount = (int)$data['transferAmount'];
                                
                                // Gọi phương thức xử lý nạp tiền
                                $result = \App\Http\Controllers\WalletController::processDepositWebhook(
                                    $userId, 
                                    $amount, 
                                    $data, 
                                    $depositCode
                                );
                                
                                return response()->json(['success' => true, 'message' => 'Giao dịch đã được xử lý']);
                            } else {
                                Log::warning('SePay Webhook API: Không tìm thấy người dùng cho mã nạp tiền', [
                                    'deposit_code' => $depositCode
                                ]);
                            }
                        }
                        
                        return response()->json(['success' => true]);
                    } else {
                        Log::warning('SePay Webhook API: Không thể trích xuất mã nạp tiền', [
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
                        
                            $order->status = 'completed';
                            $order->completed_at = Carbon::now();
                            $order->save();
                            
                            // Cập nhật trạng thái tài khoản
                            if ($order->account) {
                                $order->account->status = 'sold';
                                $order->account->save();
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
                        
                      
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'paid';
                        $order->save();
                     
                        
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
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'completed';
                        $order->completed_at = Carbon::now();
                        $order->save();
                        
                        // Cập nhật trạng thái tài khoản
                        if ($order->account) {
                            $order->account->status = 'sold';
                            $order->account->save();
                        
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
        
        // Kiểm tra loại giao dịch
        if (!isset($data['transferType'])) {
            Log::error('SePay Webhook: Thiếu thông tin loại giao dịch');
            return response()->json(['status' => 'error', 'message' => 'Thiếu thông tin giao dịch']);
        }
        
        if ($data['transferType'] === 'in') {
            // Tìm đơn hàng dựa trên mã nội dung chuyển khoản
            $pattern = config('payment.pattern', 'SEVQR');
            $content = $data['content'];
            
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
                        
                        $depositCode = 'WALLET-' . $walletCode;
                        
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
                              
                                $order = Order::where('order_number', 'like', '%' . $cleanedOrderNumber . '%')
                                    ->where('status', 'pending')
                                    ->first();
                            }
                            
                            // Phương pháp 4: Tìm kiếm với toàn bộ order_number
                            if (!$order) {
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
                        $order = Order::where('order_number', 'like', '%' . $orderNumber . '%')
                            ->where('status', 'pending')
                            ->first();
                    }
                    
                    if ($order) {
                        
                        // Cập nhật trạng thái đơn hàng bất kể số tiền
                        $order->status = 'completed';
                        $order->completed_at = Carbon::now();
                        $order->save();
                
                        
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
