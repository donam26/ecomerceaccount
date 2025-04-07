<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'account.game']);
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo người dùng
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Sắp xếp
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);
        
        $orders = $query->paginate(15);
        
        return view('admin.orders.index', compact('orders'));
    }
    
    /**
     * Hiển thị thông tin chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['user', 'account.game', 'transactions'])
            ->findOrFail($id);
            
        return view('admin.orders.show', compact('order'));
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);
        
        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        
        $order->status = $newStatus;
        
        // Nếu đơn hàng được hoàn thành, cập nhật thời gian hoàn thành
        if ($newStatus == 'completed' && $oldStatus != 'completed') {
            $order->completed_at = now();
            
            // Cập nhật trạng thái tài khoản game thành đã bán
            if ($order->account) {
                $order->account->status = 'sold';
                $order->account->save();
            }
        }
        
        // Nếu đơn hàng bị hủy, cập nhật lại trạng thái tài khoản
        if ($newStatus == 'cancelled' && $oldStatus != 'cancelled') {
            if ($order->account && $order->account->status == 'pending') {
                $order->account->status = 'available';
                $order->account->save();
            }
        }
        
        $order->save();
        
        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Đã cập nhật trạng thái đơn hàng thành công');
    }
    
    /**
     * Xóa đơn hàng
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Kiểm tra trạng thái đơn hàng
        if ($order->status != 'cancelled') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'Chỉ có thể xóa đơn hàng đã bị hủy');
        }
        
        $order->delete();
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Đã xóa đơn hàng thành công');
    }
}
