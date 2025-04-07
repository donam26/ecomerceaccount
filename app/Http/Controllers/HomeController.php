<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Account;

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
        
        $featuredAccounts = Account::where('is_featured', true)
                            ->where('status', 'available')
                            ->take(8)
                            ->get();
                            
        $recentAccounts = Account::where('status', 'available')
                          ->latest()
                          ->take(8)
                          ->get();
        
        return view('home', compact('games', 'featuredAccounts', 'recentAccounts'));
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
