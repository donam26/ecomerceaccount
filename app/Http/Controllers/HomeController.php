<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Account;
use App\Models\AccountCategory;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $games = Game::with(['accounts' => function($query) {
            $query->where('status', 'available');
        }])->take(6)->get();
        
        $recentAccounts = Account::where('status', 'available')
                          ->latest()
                          ->take(8)
                          ->get();
        
        // Lấy các danh mục tài khoản nổi bật
        $accountCategories = AccountCategory::where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('display_order')
            ->take(4)
            ->get();
        
        // Lấy các dịch vụ nổi bật hoặc mới nhất
        $services = \App\Models\GameService::with(['game', 'packages' => function($query) {
            $query->where('status', 'active')->orderBy('display_order');
        }])
        ->where('status', 'active')
        ->when(true, function($query) {
            // Ưu tiên dịch vụ có đánh dấu là nổi bật
            return $query->orderBy('is_featured', 'desc');
        })
        ->latest()
        ->take(4)
        ->get();
        
        return view('home', compact('games', 'recentAccounts', 'services', 'accountCategories'));
    }
    
    /**
     * Hiển thị trang giới thiệu
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('about');
    }
    
    /**
     * Hiển thị trang liên hệ
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('contact');
    }
    
    /**
     * Xử lý gửi form liên hệ
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Ở đây có thể thêm code để lưu thông tin liên hệ vào database
        // hoặc gửi email đến admin
        
        return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể!');
    }
}
