<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopUpService;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TopUpServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ nạp thuê
     */
    public function index(Request $request)
    {
        $query = TopUpService::with('game');
        
        // Lọc theo game nếu có
        if ($request->has('game_id') && !empty($request->game_id)) {
            $query->where('game_id', $request->game_id);
        }
        
        // Tìm kiếm theo tên
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status != 'all') {
            $query->where('is_active', $request->status === 'active');
        }
        
        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        $games = Game::orderBy('name')->get();
        
        return view('admin.topup.index', compact('services', 'games'));
    }

    /**
     * Hiển thị form tạo dịch vụ nạp thuê mới
     */
    public function create()
    {
        $games = Game::where('is_active', true)->orderBy('name')->get();
        return view('admin.topup.create', compact('games'));
    }

    /**
     * Lưu dịch vụ nạp thuê mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'estimated_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // Tạo slug
        $slug = Str::slug($request->name) . '-' . time();
        
        // Upload hình ảnh thumbnail nếu có
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('topup_services/thumbnails', 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }
        
        // Upload hình ảnh banner nếu có
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('topup_services/banners', 'public');
            $validated['banner'] = $bannerPath;
        }
        
        // Thêm slug vào dữ liệu
        $validated['slug'] = $slug;
        
        // Tạo dịch vụ mới
        TopUpService::create($validated);
        
        return redirect()->route('admin.topup.index')
            ->with('success', 'Dịch vụ nạp thuê đã được tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa dịch vụ nạp thuê
     */
    public function edit($id)
    {
        $service = TopUpService::findOrFail($id);
        $games = Game::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.topup.edit', compact('service', 'games'));
    }

    /**
     * Cập nhật dịch vụ nạp thuê
     */
    public function update(Request $request, $id)
    {
        $service = TopUpService::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'estimated_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);
        
        // Upload hình ảnh thumbnail mới nếu có
        if ($request->hasFile('thumbnail')) {
            // Xóa thumbnail cũ nếu có
            if ($service->thumbnail) {
                Storage::disk('public')->delete($service->thumbnail);
            }
            
            $thumbnailPath = $request->file('thumbnail')->store('topup_services/thumbnails', 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }
        
        // Upload hình ảnh banner mới nếu có
        if ($request->hasFile('banner')) {
            // Xóa banner cũ nếu có
            if ($service->banner) {
                Storage::disk('public')->delete($service->banner);
            }
            
            $bannerPath = $request->file('banner')->store('topup_services/banners', 'public');
            $validated['banner'] = $bannerPath;
        }
        
        // Cập nhật dịch vụ
        $service->update($validated);
        
        return redirect()->route('admin.topup.index')
            ->with('success', 'Dịch vụ nạp thuê đã được cập nhật thành công.');
    }

    /**
     * Xóa dịch vụ nạp thuê
     */
    public function destroy($id)
    {
        $service = TopUpService::findOrFail($id);
        
        // Kiểm tra xem dịch vụ có đơn hàng liên quan không
        if ($service->orders()->exists()) {
            return redirect()->route('admin.topup.index')
                ->with('error', 'Không thể xóa dịch vụ này vì đã có đơn hàng liên quan.');
        }
        
        // Xóa các hình ảnh
        if ($service->thumbnail) {
            Storage::disk('public')->delete($service->thumbnail);
        }
        
        if ($service->banner) {
            Storage::disk('public')->delete($service->banner);
        }
        
        // Xóa dịch vụ
        $service->delete();
        
        return redirect()->route('admin.topup.index')
            ->with('success', 'Dịch vụ nạp thuê đã được xóa thành công.');
    }
}
