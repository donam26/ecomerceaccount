<?php

namespace App\Http\Controllers;

use App\Models\BoostingService;
use App\Models\BoostingOrder;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Events\BoostingOrderUpdated;

class BoostingServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ cày thuê
     */
    public function index(Request $request)
    {
        $query = BoostingService::query()->where('is_active', true);
        
        // Lọc theo game nếu có
        if ($request->has('game')) {
            $query->whereHas('game', function ($q) use ($request) {
                $q->where('slug', $request->game);
            });
        }
        
        // Sắp xếp
        $sortBy = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';
        
        if (in_array($sortBy, ['name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }
        
        $services = $query->paginate(12);
        $games = Game::has('boostingServices')->get();
        
        return view('boosting.index', compact('services', 'games'));
    }
    
    /**
     * Hiển thị chi tiết dịch vụ cày thuê
     */
    public function show($slug)
    {
        $service = BoostingService::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $relatedServices = BoostingService::where('game_id', $service->game_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
            
        return view('boosting.show', compact('service', 'relatedServices'));
    }
    
    /**
     * Tạo đơn hàng dịch vụ cày thuê
     */
    public function order($slug)
    {
        $service = BoostingService::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để đặt dịch vụ cày thuê.');
        }
        
        // Tạo số đơn hàng
        $orderNumber = 'BOOST' . time() . rand(100, 999);
        
        // Tính giá tiền
        $amount = $service->getDisplayPrice();
        $originalAmount = $service->price;
        $discount = $service->hasDiscount() ? ($service->price - $service->sale_price) : 0;
        
        // Tạo đơn hàng
        $order = BoostingOrder::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'amount' => $amount,
            'original_amount' => $originalAmount,
            'discount' => $discount,
            'status' => 'pending'
        ]);
        
        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.checkout', $order->order_number);
    }
    
    /**
     * Hiển thị trang nhập thông tin tài khoản game
     */
    public function accountInfo(Request $request, $orderNumber)
    {
        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = BoostingOrder::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        // Kiểm tra nếu đơn hàng đã nhập thông tin tài khoản rồi thì chuyển hướng
        if ($order->hasAccountInfo()) {
            return redirect()->route('boosting.my_orders')
                ->with('info', 'Bạn đã cung cấp thông tin tài khoản game cho đơn hàng này rồi.');
        }
        
        // Kiểm tra trạng thái đơn hàng, chỉ những đơn hàng đã thanh toán mới có thể nhập thông tin tài khoản
        if (!$order->isPaid()) {
            return redirect()->route('payment.checkout', $orderNumber)
                ->with('warning', 'Vui lòng thanh toán đơn hàng trước khi cung cấp thông tin tài khoản.');
        }
        
        // Log thông tin để debug
        Log::info('BoostingServiceController: Hiển thị trang nhập thông tin tài khoản game', [
            'order_number' => $orderNumber,
            'user_id' => auth()->id()
        ]);
        
        return view('boosting.account_info', compact('order'));
    }

    /**
     * Xử lý form nhập thông tin tài khoản game
     */
    public function submitAccountInfo(Request $request, $orderNumber)
    {
        // Validate dữ liệu nhập vào
        $validated = $request->validate([
            'game_username' => 'required|string|max:255',
            'game_password' => 'required|string|max:255',
            'additional_info' => 'nullable|string|max:2000',
        ], [
            'game_username.required' => 'Vui lòng nhập tên đăng nhập game',
            'game_password.required' => 'Vui lòng nhập mật khẩu game',
        ]);
        
        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = BoostingOrder::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        // Kiểm tra trạng thái đơn hàng
        if (!$order->isPaid()) {
            return redirect()->route('payment.checkout', $orderNumber)
                ->with('warning', 'Vui lòng thanh toán đơn hàng trước khi cung cấp thông tin tài khoản.');
        }
        
        try {
            // Cập nhật thông tin tài khoản game
            $order->game_username = $validated['game_username'];
            $order->game_password = $validated['game_password'];
            $order->additional_info = $validated['additional_info'] ?? null;
            
            // Nếu đơn hàng đang ở trạng thái "paid" thì chuyển sang "processing"
            if ($order->status === 'paid') {
                $order->status = 'processing';
            }
            
            $order->save();
            
            // Log thông tin để debug
            Log::info('BoostingServiceController: Đã cập nhật thông tin tài khoản game', [
                'order_number' => $orderNumber,
                'status' => $order->status
            ]);
            
            // Gửi thông báo đến quản trị viên về đơn hàng cày thuê mới
            event(new \App\Events\BoostingOrderUpdated($order));
            
            // Chuyển hướng đến trang thành công
            return redirect()->route('boosting.my_orders')
                ->with('success', 'Cảm ơn bạn đã cung cấp thông tin tài khoản. Chúng tôi sẽ bắt đầu thực hiện dịch vụ ngay lập tức.');
                
        } catch (\Exception $e) {
            // Log lỗi
            Log::error('BoostingServiceController: Lỗi khi cập nhật thông tin tài khoản game', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);
            
            // Trả về lỗi
            return back()->with('error', 'Đã xảy ra lỗi khi lưu thông tin tài khoản. Vui lòng thử lại sau.');
        }
    }
    
    /**
     * Hiển thị danh sách đơn hàng cày thuê của người dùng đăng nhập
     */
    public function myOrders()
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để xem đơn hàng của bạn.');
        }
        
        // Lấy danh sách đơn hàng cày thuê của người dùng
        $orders = BoostingOrder::where('user_id', Auth::id())
            ->with(['service.game'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('boosting.my_orders', compact('orders'));
    }
}
