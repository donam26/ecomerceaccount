<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản
     */
    public function index(Request $request)
    {
        $query = Account::where('status', 'available')->with('game');
        
        // Lọc theo trò chơi
        if ($request->has('game_id') && $request->game_id) {
            $query->where('game_id', $request->game_id);
        }
        
        // Lọc theo danh mục
        if ($request->has('category_id') && $request->category_id) {
            $query->where('account_category_id', $request->category_id);
        }
        
        // Sắp xếp
        $sortBy = $request->input('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->latest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }
        
        $accounts = $query->paginate(12);
        
        // Lấy danh sách các danh mục tài khoản
        $categories = \App\Models\AccountCategory::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        return view('accounts.index', compact('accounts', 'categories'));
    }
    
    /**
     * Hiển thị chi tiết tài khoản
     */
    public function show($id)
    {
        $account = Account::with('game')->findOrFail($id);
        
        // Hiển thị tài khoản có sẵn hoặc hiển thị lỗi nếu tài khoản đã bán
        if ($account->status !== 'available') {
            return redirect()->route('accounts.index')
                ->with('error', 'Tài khoản này không còn khả dụng.');
        }
        
        // Lấy một số tài khoản liên quan
        $relatedAccounts = Account::where('game_id', $account->game_id)
            ->where('id', '!=', $account->id)
            ->where('status', 'available')
            ->take(4)
            ->get();
            
        return view('accounts.show', compact('account', 'relatedAccounts'));
    }
    
    /**
     * Tìm kiếm tài khoản
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        
        $accounts = Account::where('status', 'available')
            ->where(function($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->with('game')
            ->paginate(12);
            
        return view('accounts.search', compact('accounts', 'keyword'));
    }
}
