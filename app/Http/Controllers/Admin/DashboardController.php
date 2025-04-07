<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard
     */
    public function index()
    {
        // Thống kê tổng quan
        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_accounts' => Account::count(),
            'available_accounts' => Account::where('status', 'available')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'today_sales' => Order::where('status', 'completed')
                ->whereDate('completed_at', Carbon::today())
                ->sum('amount'),
            'month_sales' => Order::where('status', 'completed')
                ->whereMonth('completed_at', Carbon::now()->month)
                ->whereYear('completed_at', Carbon::now()->year)
                ->sum('amount'),
        ];
        
        // Đơn hàng gần đây
        $recentOrders = Order::with(['user', 'account.game'])
            ->latest()
            ->take(5)
            ->get();
            
        // Người dùng mới
        $newUsers = User::latest()
            ->take(5)
            ->get();
            
        // Tài khoản mới thêm
        $recentAccounts = Account::with('game')
            ->latest()
            ->take(5)
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recentOrders', 'newUsers', 'recentAccounts'));
    }
}
