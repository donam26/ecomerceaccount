<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản
     */
    public function index(Request $request)
    {
        $query = Account::with('game');
        
        // Lọc theo game
        if ($request->has('game_id') && $request->game_id) {
            $query->where('game_id', $request->game_id);
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Sắp xếp
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);
        
        $accounts = $query->paginate(15);
        $games = Game::orderBy('name')->get();
        
        return view('admin.accounts.index', compact('accounts', 'games'));
    }
    
    /**
     * Hiển thị form tạo mới tài khoản
     */
    public function create()
    {
        $games = Game::orderBy('name')->get();
        return view('admin.accounts.create', compact('games'));
    }
    
    /**
     * Lưu tài khoản mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,sold,pending',
        ]);
        
        $account = new Account();
        $account->game_id = $validated['game_id'];
        $account->title = $validated['title'];
        $account->description = $validated['description'] ?? null;
        $account->attributes = $validated['attributes'] ?? [];
        $account->username = $validated['username'];
        $account->password = $validated['password'];
        $account->price = $validated['price'];
        $account->original_price = $validated['original_price'] ?? null;
        $account->status = $validated['status'];
        
        // Xử lý hình ảnh
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('public/accounts', $filename);
                if ($path) {
                    $images[] = 'accounts/' . $filename;
                } else {
                    return back()->withInput()->withErrors(['images' => 'Không thể lưu hình ảnh. Vui lòng kiểm tra quyền thư mục storage.']);
                }
            }
        }
        $account->images = $images;
        
        $account->save();
        
        return redirect()->route('admin.accounts.index')
            ->with('success', 'Đã thêm tài khoản thành công');
    }
    
    /**
     * Hiển thị thông tin chi tiết tài khoản
     */
    public function show($id)
    {
        $account = Account::with('game')->findOrFail($id);
        return view('admin.accounts.show', compact('account'));
    }
    
    /**
     * Hiển thị form chỉnh sửa tài khoản
     */
    public function edit($id)
    {
        $account = Account::findOrFail($id);
        $games = Game::orderBy('name')->get();
        return view('admin.accounts.edit', compact('account', 'games'));
    }
    
    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,sold,pending',
            'delete_images' => 'nullable|array',
        ]);
        
        $account->game_id = $validated['game_id'];
        $account->title = $validated['title'];
        $account->description = $validated['description'] ?? null;
        $account->attributes = $validated['attributes'] ?? [];
        $account->username = $validated['username'];
        $account->password = $validated['password'];
        $account->price = $validated['price'];
        $account->original_price = $validated['original_price'] ?? null;
        $account->status = $validated['status'];
        
        // Xử lý xóa hình ảnh
        if ($request->has('delete_images')) {
            $currentImages = $account->images ?? [];
            $deleteImages = $validated['delete_images'];
            
            foreach ($deleteImages as $key => $value) {
                if (isset($currentImages[$key])) {
                    Storage::delete('public/' . $currentImages[$key]);
                    unset($currentImages[$key]);
                }
            }
            
            $account->images = array_values($currentImages);
        }
        
        // Xử lý thêm hình ảnh mới
        if ($request->hasFile('new_images')) {
            $currentImages = $account->images ?? [];
            if (!is_array($currentImages)) {
                $currentImages = json_decode($currentImages, true) ?? [];
            }
            
            foreach ($request->file('new_images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/accounts', $filename);
                $currentImages[] = 'accounts/' . $filename;
            }
            
            $account->images = $currentImages;
        }
        
        $account->save();
        
        return redirect()->route('admin.accounts.index')
            ->with('success', 'Đã cập nhật tài khoản thành công');
    }
    
    /**
     * Xóa tài khoản
     */
    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        
        // Xóa hình ảnh
        if (!empty($account->images) && is_array($account->images)) {
            foreach ($account->images as $image) {
                Storage::delete('public/' . $image);
            }
        }
        
        $account->delete();
        
        return redirect()->route('admin.accounts.index')
            ->with('success', 'Đã xóa tài khoản thành công');
    }
}
