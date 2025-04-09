<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Hiển thị danh sách ví của người dùng
     */
    public function index(Request $request)
    {
        $query = Wallet::with('user');
        
        // Lọc theo tìm kiếm
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }
        
        // Sắp xếp
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        
        $wallets = $query->orderBy($sort, $order)->paginate(15);
        
        return view('admin.wallets.index', compact('wallets'));
    }
    
    /**
     * Hiển thị thông tin chi tiết ví
     */
    public function show($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);
        $transactions = $wallet->transactions()
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);
        
        // Thống kê giao dịch
        $stats = [
            'total_deposit' => $wallet->transactions()
                                    ->where('type', WalletTransaction::TYPE_DEPOSIT)
                                    ->sum('amount'),
            'total_payment' => $wallet->transactions()
                                    ->where('type', WalletTransaction::TYPE_PAYMENT)
                                    ->sum('amount'),
            'total_refund' => $wallet->transactions()
                                    ->where('type', WalletTransaction::TYPE_REFUND)
                                    ->sum('amount'),
            'total_transactions' => $wallet->transactions()->count(),
        ];
        
        return view('admin.wallets.show', compact('wallet', 'transactions', 'stats'));
    }
    
    /**
     * Hiển thị form cập nhật số dư ví
     */
    public function edit($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);
        return view('admin.wallets.edit', compact('wallet'));
    }
    
    /**
     * Cập nhật thông tin ví
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);
        
        $wallet = Wallet::findOrFail($id);
        $wallet->is_active = $request->is_active;
        $wallet->save();
        
        return redirect()->route('admin.wallets.show', $wallet->id)
                        ->with('success', 'Cập nhật trạng thái ví thành công');
    }
    
    /**
     * Hiển thị form để điều chỉnh số dư ví
     */
    public function showAdjustForm($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);
        return view('admin.wallets.adjust', compact('wallet'));
    }
    
    /**
     * Xử lý điều chỉnh số dư ví
     */
    public function adjustBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'description' => 'required|string|max:255',
        ]);
        
        $wallet = Wallet::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $amount = abs($request->amount);
            
            if ($request->type == 'subtract') {
                // Kiểm tra đủ số dư
                if ($wallet->balance < $amount) {
                    return redirect()->back()->with('error', 'Số dư ví không đủ để trừ');
                }
                
                $amount = -$amount; // Chuyển thành số âm để trừ
            }
            
            // Tạo giao dịch
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = $amount;
            $transaction->balance_before = $wallet->balance;
            $transaction->balance_after = $wallet->balance + $amount;
            $transaction->type = $amount > 0 ? WalletTransaction::TYPE_DEPOSIT : WalletTransaction::TYPE_PAYMENT;
            $transaction->description = $request->description . ' (Điều chỉnh bởi Admin)';
            $transaction->reference_type = 'admin_adjustment';
            $transaction->reference_id = auth()->id();
            $transaction->save();
            
            // Cập nhật số dư ví
            $wallet->balance += $amount;
            $wallet->save();
            
            DB::commit();
            
            return redirect()->route('admin.wallets.show', $wallet->id)
                            ->with('success', 'Điều chỉnh số dư ví thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Lịch sử tất cả các giao dịch ví
     */
    public function allTransactions(Request $request)
    {
        $query = WalletTransaction::with(['wallet.user']);
        
        // Lọc theo người dùng
        if ($request->has('user_id') && $request->user_id) {
            $query->whereHas('wallet', function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        
        // Lọc theo loại giao dịch
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Lọc theo khoảng thời gian
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sắp xếp
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        
        $transactions = $query->orderBy($sort, $order)->paginate(20);
        
        // Thống kê tổng quan
        $stats = [
            'total_deposit' => WalletTransaction::where('type', WalletTransaction::TYPE_DEPOSIT)->sum('amount'),
            'total_payment' => WalletTransaction::where('type', WalletTransaction::TYPE_PAYMENT)->sum('amount'),
            'total_refund' => WalletTransaction::where('type', WalletTransaction::TYPE_REFUND)->sum('amount'),
            'total_transactions' => WalletTransaction::count(),
            'active_wallets' => Wallet::where('is_active', 1)->count(),
        ];
        
        // Danh sách người dùng cho dropdown filter
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        
        return view('admin.wallets.transactions', compact('transactions', 'stats', 'users'));
    }
    
    /**
     * Xóa giao dịch (chỉ cho admin có quyền cao)
     */
    public function deleteTransaction($id)
    {
        try {
            $transaction = WalletTransaction::findOrFail($id);
            
            // Lưu thông tin để hoàn tác số dư nếu cần
            $wallet = $transaction->wallet;
            $wallet->balance = $transaction->balance_before;
            $wallet->save();
            
            // Lưu lại thông tin giao dịch đã bị xóa
            $deletedTransactionData = [
                'wallet_id' => $transaction->wallet_id,
                'amount' => -$transaction->amount, // Đảo dấu để hoàn tác giao dịch trước đó
                'balance_before' => $wallet->balance,
                'balance_after' => $transaction->balance_before,
                'type' => $transaction->amount > 0 ? WalletTransaction::TYPE_PAYMENT : WalletTransaction::TYPE_DEPOSIT,
                'description' => 'Hủy giao dịch: ' . $transaction->description,
                'reference_type' => 'transaction_reversal',
                'reference_id' => $transaction->id,
            ];
            
            // Tạo giao dịch mới để đánh dấu việc hủy giao dịch cũ
            WalletTransaction::create($deletedTransactionData);
            
            // Xóa giao dịch cũ
            $transaction->delete();
            
            return redirect()->back()->with('success', 'Đã xóa giao dịch và hoàn tác số dư');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
