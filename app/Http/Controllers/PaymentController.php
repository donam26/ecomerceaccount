<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SePay\SePay\Facades\SePay;
use Illuminate\Support\Facades\Http;
use App\Models\BoostingOrder;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller
{
    /**
     * Hiển thị trang thanh toán
     */
    public function checkout($orderNumber)
    {
        // Tìm đơn hàng thông thường
        $order = Order::where('order_number', $orderNumber)->first();
        
        // Nếu không tìm thấy đơn hàng thông thường, kiểm tra xem có phải là đơn hàng boosting
        $isBoostingOrder = false;
        if (!$order) {
            $order = BoostingOrder::where('order_number', $orderNumber)->first();
            if (!$order) {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
            }
            
            $isBoostingOrder = true;
        }
        
        // Kiểm tra trạng thái thanh toán
        if ($order->status == 'paid' || $order->status == 'completed') {
            if ($isBoostingOrder) {
                // Nếu đơn hàng boosting đã thanh toán, chuyển đến trang nhập thông tin tài khoản
                if (!$order->hasAccountInfo()) {
                    return redirect()->route('boosting.account_info', $order->order_number)
                        ->with('success', 'Đơn hàng này đã được thanh toán, vui lòng cung cấp thông tin tài khoản');
                }
                return redirect()->route('boosting.show', $order->service->slug)
                    ->with('success', 'Đơn hàng này đã được thanh toán');
            } else {
                // Nếu đơn hàng thông thường đã thanh toán, chuyển đến trang chi tiết đơn hàng
                return redirect()->route('orders.show', $order->order_number)
                    ->with('success', 'Đơn hàng này đã được thanh toán');
            }
        }
        
        // Tạo thông tin thanh toán QR SePay
        $paymentInfo = $this->generateSePayQRCode($order);
        
        // Nếu người dùng đã đăng nhập, lấy thông tin ví điện tử
        $wallet = null;
        if (auth()->check()) {
            $wallet = auth()->user()->wallet;
        }
        
        // Hiển thị trang thanh toán với thông tin đơn hàng và QR
        return view('payment.checkout', compact('order', 'paymentInfo', 'isBoostingOrder', 'wallet'));
    }

    /**
     * Xử lý yêu cầu thanh toán
     */
    public function process(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        try {
            // Tạo thông tin thanh toán bằng phương thức generateSePayQRCode
            $paymentInfo = $this->generateSePayQRCode($order);
            
            // Lưu thông tin thanh toán
            $order->payment_method = 'sepay';
            $order->save();
            
            // Chuyển hướng người dùng đến trang thanh toán với thông tin cần thiết
            return view('payment.checkout', compact('order', 'paymentInfo'));
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thanh toán Sepay: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể kết nối đến cổng thanh toán. Vui lòng thử lại sau.');
        }
    }

    /**
     * Xử lý callback từ cổng thanh toán
     */
    public function callback($orderNumber)
    {
        // Kiểm tra loại đơn hàng
        if (strpos($orderNumber, 'BOOST') === 0) {
            $order = \App\Models\BoostingOrder::where('order_number', $orderNumber)->firstOrFail();
            
            // Kiểm tra trạng thái đơn hàng - webhook sẽ cập nhật trạng thái
            if ($order->status === 'completed' || $order->status === 'paid' || $order->status === 'processing') {
                return redirect()->route('boosting.account_info', $orderNumber);
            }
        } else {
            $order = Order::where('order_number', $orderNumber)->firstOrFail();
            
            // Kiểm tra trạng thái đơn hàng - webhook sẽ cập nhật trạng thái
            if ($order->status === 'completed') {
                return redirect()->route('payment.success', $orderNumber);
            }
        }
        
        // Chuyển hướng về trang đơn hàng nếu chưa hoàn thành
        if(strpos($orderNumber, 'BOOST') === 0) {
            return redirect()->route('boosting.my_orders')
                ->with('info', 'Đơn hàng đang được xử lý, vui lòng chờ trong giây lát.');
        } else {
            return redirect()->route('orders.show', $orderNumber)
                ->with('info', 'Đơn hàng đang được xử lý, vui lòng chờ trong giây lát.');
        }
    }

    /**
     * Hiển thị trang thanh toán thành công
     */
    public function success($orderNumber)
    {
        // Kiểm tra xem đơn hàng thuộc loại nào dựa vào prefix
        if (strpos($orderNumber, 'BOOST') === 0) {
            // Đơn hàng cày thuê
            $boostingOrder = \App\Models\BoostingOrder::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            // Cập nhật trạng thái thành "paid" nếu chưa được thanh toán
            if ($boostingOrder->status === 'pending') {
                $boostingOrder->status = 'paid';
                $boostingOrder->save();
                
                // Log cập nhật trạng thái
                Log::info('PaymentController: Đã cập nhật đơn hàng cày thuê thành paid', [
                    'order_number' => $boostingOrder->order_number
                ]);
            }
                
            // Nếu là đơn hàng cày thuê đã thanh toán và chưa cung cấp thông tin tài khoản,
            // chuyển hướng đến trang nhập thông tin tài khoản
            if ($boostingOrder->isPaid() && !$boostingOrder->hasAccountInfo()) {
                Log::info('PaymentController: Chuyển hướng đến trang nhập thông tin tài khoản', [
                    'order_number' => $boostingOrder->order_number,
                    'route' => 'boosting.account_info'
                ]);
                
                return redirect()->route('boosting.account_info', $orderNumber)
                    ->with('success', 'Thanh toán thành công! Vui lòng cung cấp thông tin tài khoản game để chúng tôi thực hiện dịch vụ.');
            }
            
            // Hiển thị trang thanh toán thành công cho dịch vụ cày thuê
            Log::info('PaymentController: Hiển thị trang thanh toán thành công', [
                'order_number' => $boostingOrder->order_number,
                'status' => $boostingOrder->status,
                'has_account_info' => $boostingOrder->hasAccountInfo()
            ]);
            
            return view('payment.success', [
                'order' => $boostingOrder, 
                'isBoostingOrder' => true
            ]);
        } else {
            // Đơn hàng thường
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            // Cập nhật trạng thái thành "completed" nếu chưa được thanh toán
            if ($order->status === 'pending') {
                $order->status = 'completed';
                $order->save();
            }
            
            // Hiển thị trang thanh toán thành công
            return view('payment.success', [
                'order' => $order,
                'isBoostingOrder' => false
            ]);
        }
    }

    /**
     * Tạo QR code từ SePay API
     * 
     * @param mixed $order           Đơn hàng cần thanh toán (có thể là Order hoặc BoostingOrder)
     * @param string $paymentContent Nội dung thanh toán (tuỳ chọn)
     * @return array                 Trả về mảng chứa thông tin thanh toán: qr_url, payment_content, amount, order_number
     */
    private function generateSePayQRCode($order, $paymentContent = null)
    {
        // Nếu không có nội dung chuyển khoản thì tạo từ mẫu
        if ($paymentContent === null) {
            $pattern = config('payment.pattern', 'SEVQR');
            
            // Xác định loại đơn hàng dựa trên prefix của order_number hoặc class
            $orderNumber = $order->order_number;
            $orderType = "ORDPRODUCT";
            
            // Kiểm tra nếu là đơn hàng cày thuê
            if (strpos($orderNumber, 'BOOST') === 0 || $order instanceof \App\Models\BoostingOrder) {
                $orderType = "ORDBOOST";
            } 
            // Kiểm tra nếu là nạp ví (nếu có)
            elseif (strpos($orderNumber, 'WALLET') === 0) {
                $orderType = "ORDWALLET";
            }
            
            // Tạo nội dung chuyển khoản không có dấu gạch ngang
            // Xử lý mã đơn hàng cho phù hợp với yêu cầu thanh toán
            if (strpos($orderNumber, 'ORD-') === 0) {
                // Đối với đơn hàng thường, sử dụng toàn bộ mã đơn hàng, chỉ loại bỏ "ORD-"
                $cleanedOrderNumber = str_replace('ORD-', '', $orderNumber);
            } else if (strpos($orderNumber, 'BOOST') === 0) {
                // Đối với đơn hàng cày thuê, loại bỏ "BOOST"
                $cleanedOrderNumber = str_replace('BOOST', '', $orderNumber);
            } else if (strpos($orderNumber, 'WALLET-') === 0) {
                // Đối với nạp ví, loại bỏ "WALLET-"
                $cleanedOrderNumber = str_replace('WALLET-', '', $orderNumber);
            } else {
                // Trường hợp khác giữ nguyên
                $cleanedOrderNumber = $orderNumber;
            }
            
            $paymentContent = $pattern . ' ' . $orderType . $cleanedOrderNumber;
            
            // Log nội dung chuyển khoản để debug
            Log::info('SePay QR: Tạo nội dung chuyển khoản', [
                'orderNumber' => $orderNumber,
                'cleanedOrderNumber' => $cleanedOrderNumber,
                'orderType' => $orderType,
                'paymentContent' => $paymentContent
            ]);
        }
        
        // Mã hóa nội dung chuyển khoản để sử dụng trong URL
        $encodedContent = urlencode($paymentContent);
        
        // Số tiền đã được định dạng
        $amount = (int)$order->amount;
        
        // Tạo URL trực tiếp đến QR code của SePay
        $qrUrl = "https://qr.sepay.vn/img?acc=103870429701&bank=VietinBank&amount={$amount}&des={$encodedContent}&template=compact";
        
        // Log URL để debug
        Log::debug('SePay QR URL', [
            'url' => $qrUrl,
            'content' => $paymentContent,
            'order_number' => $order->order_number
        ]);
        
        return [
            'qr_url' => $qrUrl,
            'payment_content' => $paymentContent,
            'amount' => $amount,
            'order_number' => $order->order_number
        ];
    }

    /**
     * Xử lý thanh toán đơn hàng qua ví
     */
    public function processWalletPayment($orderNumber)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để sử dụng thanh toán qua ví');
        }
        
        $user = auth()->user();
        $wallet = $user->wallet;
        
        // Nếu người dùng chưa có ví, tạo ví mới
        if (!$wallet) {
            return redirect()->route('wallet.deposit')->with('error', 'Bạn chưa có ví điện tử. Vui lòng nạp tiền để tạo ví mới.');
        }
        
        // Tìm đơn hàng cần thanh toán
        $order = Order::where('order_number', $orderNumber)->first();
        
        // Nếu không tìm thấy đơn hàng thông thường, kiểm tra xem có phải là đơn hàng boosting
        $isBoostingOrder = false;
        if (!$order) {
            $order = BoostingOrder::where('order_number', $orderNumber)->first();
            if (!$order) {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng');
            }
            
            $isBoostingOrder = true;
        }
        
        // Kiểm tra trạng thái đơn hàng
        if ($order->status == 'paid' || $order->status == 'completed') {
            if ($isBoostingOrder) {
                return redirect()->route('boosting.account_info', $order->order_number)->with('success', 'Đơn hàng này đã được thanh toán');
            } else {
                return redirect()->route('payment.success', $order->order_number)->with('success', 'Đơn hàng này đã được thanh toán');
            }
        }
        
        // Kiểm tra số dư ví
        if ($wallet->balance < $order->amount) {
            return redirect()->route('wallet.deposit')->with('error', 'Số dư ví không đủ để thanh toán. Vui lòng nạp thêm tiền.');
        }
        
        try {
            // Tạo giao dịch thanh toán
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = -$order->amount; // Số tiền âm vì là thanh toán
            $transaction->balance_before = $wallet->balance;
            $transaction->balance_after = $wallet->balance - $order->amount;
            $transaction->type = WalletTransaction::TYPE_PAYMENT;
            $transaction->description = 'Thanh toán đơn hàng #' . $order->order_number;
            $transaction->reference_id = $order->id;
            $transaction->reference_type = $isBoostingOrder ? 'boosting_order' : 'order';
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance = $transaction->balance_after;
            $wallet->save();
            
            // Cập nhật trạng thái đơn hàng
            $order->status = 'paid';
            $order->payment_method = 'wallet';
            $order->paid_at = now();
            $order->save();
            
            // Chuyển hướng tùy theo loại đơn hàng
            if ($isBoostingOrder) {
                return redirect()->route('boosting.account_info', $order->order_number)->with('success', 'Thanh toán thành công. Vui lòng nhập thông tin tài khoản.');
            } else {
                return redirect()->route('payment.success', $order->order_number)->with('success', 'Thanh toán thành công. Cảm ơn bạn đã mua hàng!');
            }
        } catch (\Exception $e) {
            Log::error('Lỗi thanh toán qua ví: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi trong quá trình thanh toán. Vui lòng thử lại sau.');
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán đơn hàng
     */
    public function checkStatus($orderNumber)
    {
        // Kiểm tra xem có phải là mã nạp tiền vào ví không
        if (strpos($orderNumber, 'WALLET-') === 0) {
            // Tìm bản ghi nạp tiền trong database
            $walletDeposit = \App\Models\WalletDeposit::where('deposit_code', $orderNumber)->first();
            
            if ($walletDeposit) {
                if ($walletDeposit->isCompleted()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Nạp tiền vào ví thành công',
                        'status' => 'completed',
                        'redirect_url' => route('wallet.index')
                    ]);
                } else {
                    // Chưa hoàn thành, trả về trạng thái chờ
                    return response()->json([
                        'success' => false,
                        'message' => 'Đang chờ thanh toán',
                        'status' => $walletDeposit->status
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mã nạp tiền',
                'status' => 'not_found'
            ]);
        }
        
        // Tìm đơn hàng cần kiểm tra
        $order = Order::where('order_number', $orderNumber)->first();
        
        // Nếu không tìm thấy đơn hàng thông thường, kiểm tra xem có phải là đơn hàng boosting
        $isBoostingOrder = false;
        if (!$order) {
            $order = BoostingOrder::where('order_number', $orderNumber)->first();
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'status' => 'not_found'
                ]);
            }
            
            $isBoostingOrder = true;
        }
        
        // Trả về trạng thái đơn hàng
        if ($order->status == 'paid' || $order->status == 'completed') {
            $redirectUrl = $isBoostingOrder 
                ? route('boosting.account_info', $order->order_number)
                : route('payment.success', $order->order_number);
                
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được thanh toán thành công',
                'status' => $order->status,
                'redirect_url' => $redirectUrl
            ]);
        } elseif ($order->status == 'processing') {
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đang được xử lý',
                'status' => 'processing'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa được thanh toán',
                'status' => $order->status
            ]);
        }
    }
}
