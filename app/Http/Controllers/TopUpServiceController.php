<?php

namespace App\Http\Controllers;

use App\Models\TopUpService;
use App\Models\TopUpOrder;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopUpServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ nạp thuê
     */
    public function index(Request $request)
    {
        $query = TopUpService::query()->where('is_active', true);
        
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
        $games = Game::has('topUpServices')->get();
        
        return view('topup.index', compact('services', 'games'));
    }
    
    /**
     * Hiển thị chi tiết dịch vụ nạp thuê
     */
    public function show($slug)
    {
        $service = TopUpService::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $relatedServices = TopUpService::where('game_id', $service->game_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
            
        return view('topup.show', compact('service', 'relatedServices'));
    }
    
    /**
     * Tạo đơn hàng dịch vụ nạp thuê
     */
    public function order(Request $request, $slug)
    {
        $service = TopUpService::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để đặt dịch vụ nạp thuê.');
        }
        
        // Xác định quy tắc xác thực dựa trên login_type
        $rules = [];
        $messages = [];
        
        // Kiểm tra login_type và thêm quy tắc xác thực phù hợp
        if ($service->login_type === 'game_id' || $service->login_type === 'both') {
            $rules['game_id'] = 'required|string|max:255';
            $messages['game_id.required'] = 'Vui lòng nhập ID trong game của bạn';
        }
        
        if ($service->login_type === 'username_password' || $service->login_type === 'both') {
            $rules['game_username'] = 'required|string|max:255';
            $rules['game_password'] = 'required|string|max:255';
            $messages['game_username.required'] = 'Vui lòng nhập tên đăng nhập game của bạn';
            $messages['game_password.required'] = 'Vui lòng nhập mật khẩu game của bạn';
        }
        
        // Thêm các trường khác
        $rules['server_id'] = 'nullable|string|max:255';
        $rules['additional_info'] = 'nullable|string|max:1000';
        
        // Validate thông tin
        $request->validate($rules, $messages);

        // Tạo số đơn hàng
        $orderNumber = 'TOPUP' . time() . rand(100, 999);
        
        // Tính giá tiền
        $amount = $service->getDisplayPrice();
        $originalAmount = $service->price;
        $discount = $service->hasDiscount() ? ($service->price - $service->sale_price) : 0;
        
        // Chuẩn bị dữ liệu đơn hàng
        $orderData = [
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'amount' => $amount,
            'original_amount' => $originalAmount,
            'discount' => $discount,
            'status' => 'pending',
            'server_id' => $request->server_id,
            'additional_info' => $request->additional_info,
        ];
        
        // Thêm thông tin đăng nhập dựa trên login_type
        if ($service->login_type === 'game_id' || $service->login_type === 'both') {
            $orderData['game_id'] = $request->game_id;
        }
        
        // Cập nhật TopUpOrder model để có thêm các trường game_username và game_password
        if ($service->login_type === 'username_password' || $service->login_type === 'both') {
            $orderData['game_username'] = $request->game_username;
            $orderData['game_password'] = $request->game_password;
        }
        
        // Tạo đơn hàng
        $order = TopUpOrder::create($orderData);
        
        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.checkout', $order->order_number);
    }
    
    /**
     * Hiển thị danh sách đơn hàng nạp thuê của người dùng đăng nhập
     */
    public function myOrders()
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Vui lòng đăng nhập để xem đơn hàng của bạn.');
        }
        
        // Lấy danh sách đơn hàng nạp thuê của người dùng
        $orders = TopUpOrder::where('user_id', Auth::id())
            ->with(['service.game'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('topup.my_orders', compact('orders'));
    }
    
    /**
     * Hiển thị chi tiết đơn hàng nạp thuê
     */
    public function showOrder($orderNumber)
    {
        // Lấy thông tin đơn hàng từ cơ sở dữ liệu
        $order = TopUpOrder::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with(['service.game'])
            ->firstOrFail();
            
        return view('topup.order_detail', compact('order'));
    }
}
