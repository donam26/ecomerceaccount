<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Hiển thị danh sách trò chơi
     */
    public function index()
    {
        $games = Game::orderBy('name')->get();
        return view('admin.games.index', compact('games'));
    }
    
    /**
     * Hiển thị form tạo mới trò chơi
     */
    public function create()
    {
        return view('admin.games.create');
    }
    
    /**
     * Lưu trò chơi mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $game = new Game();
        $game->name = $validated['name'];
        $game->slug = $validated['slug'];
        $game->description = $validated['description'] ?? null;
        
        // Xử lý hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/games', $filename);
            $game->image = 'games/' . $filename;
        }
        
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $filename = time() . '_banner_' . $banner->getClientOriginalName();
            $banner->storeAs('public/games', $filename);
            $game->banner = 'games/' . $filename;
        }
        
        $game->save();
        
        return redirect()->route('admin.games.index')
            ->with('success', 'Đã thêm trò chơi thành công');
    }
    
    /**
     * Hiển thị thông tin chi tiết trò chơi
     */
    public function show($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.show', compact('game'));
    }
    
    /**
     * Hiển thị form chỉnh sửa trò chơi
     */
    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }
    
    /**
     * Cập nhật thông tin trò chơi
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $game->name = $validated['name'];
        $game->slug = $validated['slug'];
        $game->description = $validated['description'] ?? null;
        
        // Xử lý hình ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/games', $filename);
            $game->image = 'games/' . $filename;
        }
        
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $filename = time() . '_banner_' . $banner->getClientOriginalName();
            $banner->storeAs('public/games', $filename);
            $game->banner = 'games/' . $filename;
        }
        
        $game->save();
        
        return redirect()->route('admin.games.index')
            ->with('success', 'Đã cập nhật trò chơi thành công');
    }
    
    /**
     * Xóa trò chơi
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();
        
        return redirect()->route('admin.games.index')
            ->with('success', 'Đã xóa trò chơi thành công');
    }
}
