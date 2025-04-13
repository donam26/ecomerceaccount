<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\Game;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    /**
     * Hiển thị danh sách các danh mục tài khoản
     */
    public function index()
    {
        $categories = AccountCategory::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        return view('account.categories.index', compact('categories'));
    }
    
    /**
     * Hiển thị danh sách tài khoản theo danh mục
     */
    public function show($slug)
    {
        $category = AccountCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $query = Account::where('account_category_id', $category->id)
            ->where('status', 'available');
        
        // Sắp xếp mặc định theo mới nhất
        $query->orderBy('created_at', 'desc');
        
        $accounts = $query->paginate(12);
        
        // Lấy list game để lọc
        $games = Game::whereHas('accounts', function($q) use ($category) {
            $q->where('account_category_id', $category->id)
                ->where('status', 'available');
        })->get();
        
        return view('account.categories.show', compact('category', 'accounts', 'games'));
    }
    
    /**
     * Lọc tài khoản theo danh mục và các tiêu chí khác
     */
    public function filter(Request $request, $slug)
    {
        $category = AccountCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        $query = Account::where('account_category_id', $category->id)
            ->where('status', 'available');
        
        // Lọc theo trò chơi
        if ($request->has('game_id') && $request->game_id) {
            $query->where('game_id', $request->game_id);
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
            case 'latest':
            default:
                $query->latest();
                break;
        }
        
        $accounts = $query->paginate(12);
        
        // Lấy list game để lọc
        $games = Game::whereHas('accounts', function($q) use ($category) {
            $q->where('account_category_id', $category->id)
                ->where('status', 'available');
        })->get();
        
        return view('account.categories.show', compact('category', 'accounts', 'games'));
    }
} 