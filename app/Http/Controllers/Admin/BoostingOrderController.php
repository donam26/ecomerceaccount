<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostingOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BoostingOrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng cày thuê
     */
    public function index(Request $request)
    {
        $query = BoostingOrder::with(['user', 'service', 'assignedTo']);

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo mã đơn hàng
        if ($request->has('search') && !empty($request->search)) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Sắp xếp
        $sortBy = $request->sort ?? 'created_at';
        $sortOrder = $request->order ?? 'desc';

        if (in_array($sortBy, ['order_number', 'created_at', 'status', 'amount'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $orders = $query->paginate(15);

        // Thống kê đơn hàng theo trạng thái
        $stats = DB::table('boosting_orders')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.boosting_orders.index', compact('orders', 'stats'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = BoostingOrder::with(['user', 'service', 'assignedTo'])
            ->findOrFail($id);

        // Lấy danh sách nhân viên có thể giao việc
        $staffs = User::whereHas('role', function ($query) {
                $query->whereIn('slug', ['admin', 'staff']);
            })
            ->orderBy('name')
            ->get();

        return view('admin.boosting_orders.show', compact('order', 'staffs'));
    }

    /**
     * Gán đơn hàng cho nhân viên
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $order = BoostingOrder::findOrFail($id);
        $order->assigned_to = $request->assigned_to;
        $order->save();

        return redirect()->route('admin.boosting_orders.show', $id)
            ->with('success', 'Đơn hàng đã được gán cho nhân viên thành công.');
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled',
            'admin_notes' => 'nullable|string'
        ]);

        $order = BoostingOrder::findOrFail($id);
        $oldStatus = $order->status;
        
        // Cập nhật trạng thái
        $order->status = $request->status;
        
        // Thêm ghi chú nếu có
        if ($request->filled('admin_notes')) {
            $order->admin_notes = $request->admin_notes;
        }
        
        // Nếu đánh dấu hoàn thành
        if ($request->status === 'completed' && $oldStatus !== 'completed') {
            $order->completed_at = now();
        }
        
        $order->save();

        return redirect()->route('admin.boosting_orders.show', $id)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật thành công.');
    }

    /**
     * Xem thông tin tài khoản game
     */
    public function viewGameAccount($id)
    {
        $order = BoostingOrder::with(['user', 'service'])
            ->findOrFail($id);
        
        // Kiểm tra nếu không có thông tin tài khoản
        if (!$order->hasAccountInfo()) {
            return redirect()->route('admin.boosting_orders.show', $id)
                ->with('error', 'Đơn hàng này chưa có thông tin tài khoản game.');
        }
        
        // Ghi log ai đã xem thông tin này
        \Illuminate\Support\Facades\Log::info('Admin viewed game account info', [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'timestamp' => now()
        ]);
        
        return view('admin.boosting_orders.view_account', compact('order'));
    }
    
    /**
     * Cập nhật ghi chú cho đơn hàng
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);

        $order = BoostingOrder::findOrFail($id);
        $order->admin_notes = $request->admin_notes;
        $order->save();
        
        // Ghi log hành động
        \Illuminate\Support\Facades\Log::info('Admin updated order notes', [
            'admin_id' => Auth::id(),
            'admin_name' => Auth::user()->name,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'timestamp' => now()
        ]);

        return redirect()->route('admin.boosting_orders.show', $id)
            ->with('success', 'Ghi chú đơn hàng đã được cập nhật thành công.');
    }
}
