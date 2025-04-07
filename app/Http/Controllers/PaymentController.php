<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SePay\SePay\Facades\SePay;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Hiển thị trang thanh toán
     */
    public function checkout($orderNumber)
    {
        // Kiểm tra xem đơn hàng thuộc loại nào dựa vào prefix
        if (strpos($orderNumber, 'BOOST') === 0) {
            // Đơn hàng cày thuê
            $order = \App\Models\BoostingOrder::where('order_number', $orderNumber)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();
                
            $isBoostingOrder = true;
        } else {
            // Đơn hàng thường
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();
                
            $isBoostingOrder = false;
        }

        // Lấy thông tin cấu hình SePay từ config
        $pattern = config('payment.pattern', 'SEVQR');
        
        // Tạo nội dung chuyển khoản theo định dạng của SePay
        if (strpos($order->order_number, 'ORD') === 0) {
            // Nếu đã có ORD thì không thêm vào nữa
            $paymentContent = $pattern . ' ' . $order->order_number;
        } else {
            // Nếu chưa có thì thêm vào
            $paymentContent = $pattern . ' ORD' . $order->order_number;
        }
        
        // Chuẩn bị dữ liệu hiển thị thông tin thanh toán
        $paymentInfo = [
            'amount' => $order->amount,
            'payment_content' => $paymentContent,
            'order_number' => $order->order_number,
        ];
        
        // Tạo QR code từ SePay
        try {
            // Tạo QR từ SePay API
            $qrCode = $this->generateSePayQRCode($order);
            $paymentInfo['qr_image'] = $qrCode;
        } catch (\Exception $e) {
            // Log lỗi
            Log::error('Lỗi tạo QR code SePay: ' . $e->getMessage());
            
            // Fallback đến QR code VietQR nếu SePay gặp lỗi
            $paymentInfo['qr_image'] = null;
        }

        return view('payment.checkout', compact('order', 'paymentInfo', 'isBoostingOrder'));
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
            // Lấy thông tin cấu hình SePay từ config
            $pattern = config('payment.pattern', 'SEVQR');
            
            // Tạo nội dung chuyển khoản theo định dạng của SePay
            if (strpos($order->order_number, 'ORD') === 0) {
                // Nếu đã có ORD thì không thêm vào nữa
                $paymentContent = $pattern . ' ' . $order->order_number;
            } else {
                // Nếu chưa có thì thêm vào
                $paymentContent = $pattern . ' ORD' . $order->order_number;
            }
            
            // Chuẩn bị dữ liệu hiển thị thông tin thanh toán
            $paymentInfo = [
                'amount' => $order->amount,
                'payment_content' => $paymentContent,
                'order_number' => $order->order_number,
            ];
            
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
     * @param string $paymentContent Nội dung thanh toán
     * @return string                URL hình ảnh QR hoặc dữ liệu Base64
     */
    private function generateSePayQRCode($order, $paymentContent = null)
    {
        // Nếu không có nội dung chuyển khoản thì tạo từ mẫu
        if ($paymentContent === null) {
            $pattern = config('payment.pattern', 'SEVQR');
            
            // Kiểm tra mã đơn hàng đã có prefix "ORD" chưa
            $orderNumber = $order->order_number;
            if (strpos($orderNumber, 'ORD') === 0) {
                // Nếu đã có ORD thì không thêm vào nữa
                $paymentContent = $pattern . ' ' . $orderNumber;
            } else {
                // Nếu chưa có thì thêm vào
                $paymentContent = $pattern . ' ORD' . $orderNumber;
            }
            
            // Log nội dung chuyển khoản để debug
            Log::debug('Nội dung chuyển khoản', [
                'orderNumber' => $orderNumber,
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
        
        return $qrUrl;
    }
}
