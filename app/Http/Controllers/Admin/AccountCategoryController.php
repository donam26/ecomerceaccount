<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountCategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục tài khoản
     */
    public function index()
    {
        $categories = AccountCategory::orderBy('display_order')
            ->orderBy('name')
            ->paginate(15);
        
        return view('admin.account_categories.index', compact('categories'));
    }
    
    /**
     * Hiển thị form tạo danh mục mới
     */
    public function create()
    {
        return view('admin.account_categories.create');
    }
    
    /**
     * Lưu danh mục mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);
        
        $category = new AccountCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->is_active = $request->has('is_active');
        $category->is_featured = $request->has('is_featured');
        $category->display_order = $request->display_order ?? 0;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('account-categories', 'public');
            $category->image = $path;
        }
        
        $category->save();
        
        return redirect()->route('admin.account_categories.index')
            ->with('success', 'Danh mục tài khoản đã được tạo thành công');
    }
    
    /**
     * Hiển thị chi tiết danh mục
     */
    public function show(AccountCategory $accountCategory)
    {
        return view('admin.account_categories.show', compact('accountCategory'));
    }
    
    /**
     * Hiển thị form chỉnh sửa danh mục
     */
    public function edit(AccountCategory $accountCategory)
    {
        return view('admin.account_categories.edit', compact('accountCategory'));
    }
    
    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, AccountCategory $accountCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);
        
        $accountCategory->name = $request->name;
        $accountCategory->slug = Str::slug($request->name);
        $accountCategory->description = $request->description;
        $accountCategory->is_active = $request->has('is_active');
        $accountCategory->is_featured = $request->has('is_featured');
        $accountCategory->display_order = $request->display_order ?? 0;
        
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($accountCategory->image) {
                Storage::disk('public')->delete($accountCategory->image);
            }
            
            // Lưu ảnh mới
            $path = $request->file('image')->store('account-categories', 'public');
            $accountCategory->image = $path;
        }
        
        $accountCategory->save();
        
        return redirect()->route('admin.account_categories.index')
            ->with('success', 'Danh mục tài khoản đã được cập nhật thành công');
    }
    
    /**
     * Xóa danh mục
     */
    public function destroy(AccountCategory $accountCategory)
    {
        // Xóa ảnh nếu có
        if ($accountCategory->image) {
            Storage::disk('public')->delete($accountCategory->image);
        }
        
        $accountCategory->delete();
        
        return redirect()->route('admin.account_categories.index')
            ->with('success', 'Danh mục tài khoản đã được xóa thành công');
    }
} 