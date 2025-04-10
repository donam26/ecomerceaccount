<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index(Request $request)
    {
        $query = User::with('role');
        
        // Lọc theo role
        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }
        
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Sắp xếp
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);
        
        $users = $query->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }
    
    /**
     * Hiển thị form tạo mới người dùng
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * Lưu người dùng mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->password = Hash::make($validated['password']);
        $user->role_id = $validated['role_id'];
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Đã thêm người dùng thành công');
    }
    
    /**
     * Hiển thị thông tin chi tiết người dùng
     */
    public function show(string $id)
    {
        $user = User::with('orders', 'role')->findOrFail($id);
        
        // Lấy ví của người dùng (nếu có)
        $wallet = $user->wallet;
        
        // Lấy các giao dịch gần đây của ví (nếu có)
        $walletTransactions = [];
        if ($wallet) {
            $walletTransactions = $wallet->transactions()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('admin.users.show', compact('user', 'wallet', 'walletTransactions'));
    }
    
    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role_id = $validated['role_id'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Đã cập nhật người dùng thành công');
    }
    
    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Không cho phép xóa người dùng là admin
        if ($user->role && $user->role->slug === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản quản trị viên');
        }
        
        // Không cho phép xóa người dùng đã có đơn hàng
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa người dùng đã có đơn hàng');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa người dùng thành công');
    }
    
    /**
     * Tạo ví mới cho người dùng
     */
    public function createWallet($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra nếu người dùng đã có ví
        if ($user->wallet) {
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Người dùng này đã có ví điện tử');
        }
        
        // Tạo ví mới
        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->balance = 0;
        $wallet->is_active = true;
        $wallet->save();
        
        return redirect()->route('admin.users.show', $id)
            ->with('success', 'Đã tạo ví điện tử mới cho người dùng');
    }
    
    /**
     * Hiển thị form điều chỉnh số dư ví
     */
    public function showWalletAdjustForm($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra nếu người dùng chưa có ví
        if (!$user->wallet) {
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Người dùng này chưa có ví điện tử');
        }
        
        return view('admin.users.wallet-adjust', compact('user'));
    }
    
    /**
     * Xử lý điều chỉnh số dư ví
     */
    public function adjustWallet(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'description' => 'required|string|max:255',
        ]);
        
        $user = User::findOrFail($id);
        
        // Kiểm tra nếu người dùng chưa có ví
        if (!$user->wallet) {
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Người dùng này chưa có ví điện tử');
        }
        
        $wallet = $user->wallet;
        
        try {
            DB::beginTransaction();
            
            $amount = abs($request->amount);
            
            if ($request->type == 'add') {
                // Thêm tiền vào ví
                $wallet->deposit(
                    $amount,
                    'deposit',
                    $request->description . ' (Điều chỉnh bởi Admin)',
                    auth()->id(),
                    'admin_adjustment'
                );
            } else {
                // Kiểm tra đủ số dư
                if ($wallet->balance < $amount) {
                    return redirect()->back()->with('error', 'Số dư ví không đủ để trừ');
                }
                
                // Trừ tiền từ ví
                $wallet->withdraw(
                    $amount,
                    'payment',
                    $request->description . ' (Điều chỉnh bởi Admin)',
                    auth()->id(),
                    'admin_adjustment'
                );
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.show', $id)
                ->with('success', 'Điều chỉnh số dư ví thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị tất cả giao dịch của ví người dùng
     */
    public function showWalletTransactions($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra nếu người dùng chưa có ví
        if (!$user->wallet) {
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Người dùng này chưa có ví điện tử');
        }
        
        $transactions = $user->wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.users.wallet-transactions', compact('user', 'transactions'));
    }
    
    /**
     * Bật/tắt trạng thái ví
     */
    public function toggleWalletStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Kiểm tra nếu người dùng chưa có ví
        if (!$user->wallet) {
            return redirect()->route('admin.users.show', $id)
                ->with('error', 'Người dùng này chưa có ví điện tử');
        }
        
        $wallet = $user->wallet;
        $wallet->is_active = !$wallet->is_active;
        $wallet->save();
        
        $message = $wallet->is_active ? 'Đã mở khóa ví điện tử' : 'Đã khóa ví điện tử';
        
        return redirect()->route('admin.users.show', $id)
            ->with('success', $message);
    }
}
