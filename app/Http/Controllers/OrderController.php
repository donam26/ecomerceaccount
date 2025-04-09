<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng của người dùng hiện tại
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('orders.index', compact('orders'));
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        // Tìm thông tin tài khoản nếu đơn hàng đã hoàn thành
        $accountInfo = null;
        if ($order->status === 'completed') {
            if ($order->account_details) {
                $accountInfo = json_decode($order->account_details, true);
            } elseif ($order->account) {
                // Nếu không có thông tin cached, lấy trực tiếp từ account
                $attributes = $order->account->attributes;
                // Kiểm tra nếu attributes là string thì mới json_decode
                $extraInfo = is_string($attributes) ? json_decode($attributes, true) : $attributes;
                
                $accountInfo = [
                    'username' => $order->account->username,
                    'password' => $order->account->password,
                    'extra_info' => $extraInfo,
                    'game_name' => $order->account->game->name,
                ];
            }
        }
        
        return view('orders.show', compact('order', 'accountInfo'));
    }
    
    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
        ]);
        
        $account = Account::findOrFail($validated['account_id']);
        
        // Kiểm tra tài khoản còn khả dụng
        if (!$account->isAvailable()) {
            return back()->with('error', 'Tài khoản này đã được bán.');
        }
        
        // Tạo đơn hàng
        $order = Order::create([
            'user_id' => Auth::id(),
            'account_id' => $account->id,
            'order_number' => Order::generateOrderNumber(),
            'amount' => $account->price,
            'status' => 'pending',
        ]);
        
        // Cập nhật trạng thái tài khoản
        $account->update(['status' => 'pending']);
        
        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.checkout', $order->order_number);
    }
    
    /**
     * Hủy đơn hàng đang chờ thanh toán
     */
    public function cancel($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        // Cập nhật trạng thái đơn hàng
        $order->status = 'cancelled';
        $order->save();
        
        // Đưa tài khoản về trạng thái có sẵn
        $order->account->status = 'available';
        $order->account->save();
        
        return redirect()->route('orders.show', $orderNumber)
            ->with('success', 'Đơn hàng đã được hủy thành công.');
    }
}
