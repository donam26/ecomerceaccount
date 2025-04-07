<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostingService;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BoostingServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ cày thuê
     */
    public function index()
    {
        $services = BoostingService::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.boosting.index', compact('services'));
    }

    /**
     * Hiển thị form tạo dịch vụ cày thuê mới
     */
    public function create()
    {
        $games = Game::where('is_active', true)->orderBy('name')->get();
        return view('admin.boosting.create', compact('games'));
    }

    /**
     * Lưu dịch vụ cày thuê mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'estimated_days' => 'required|integer|min:1',
            'requirements' => 'nullable|string',
            'includes' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        // Xử lý slug
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;

        while (BoostingService::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        // Xử lý ảnh thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('boosting/thumbnails', 'public');
        }

        // Xử lý ảnh banner
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('boosting/banners', 'public');
        }

        // Tạo dịch vụ mới
        BoostingService::create([
            'name' => $request->name,
            'slug' => $slug,
            'game_id' => $request->game_id,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'estimated_days' => $request->estimated_days,
            'requirements' => $request->requirements,
            'includes' => $request->includes,
            'thumbnail' => $thumbnailPath,
            'banner' => $bannerPath,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('admin.boosting.index')
            ->with('success', 'Dịch vụ cày thuê đã được tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa dịch vụ cày thuê
     */
    public function edit($id)
    {
        $service = BoostingService::findOrFail($id);
        $games = Game::where('is_active', true)->orderBy('name')->get();
        return view('admin.boosting.edit', compact('service', 'games'));
    }

    /**
     * Cập nhật dịch vụ cày thuê
     */
    public function update(Request $request, $id)
    {
        $service = BoostingService::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'estimated_days' => 'required|integer|min:1',
            'requirements' => 'nullable|string',
            'includes' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        // Xử lý slug nếu tên thay đổi
        if ($service->name !== $request->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $count = 1;

            while (BoostingService::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            $service->slug = $slug;
        }

        // Xử lý ảnh thumbnail
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu có
            if ($service->thumbnail && Storage::disk('public')->exists($service->thumbnail)) {
                Storage::disk('public')->delete($service->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail')->store('boosting/thumbnails', 'public');
            $service->thumbnail = $thumbnailPath;
        }

        // Xử lý ảnh banner
        if ($request->hasFile('banner')) {
            // Xóa ảnh cũ nếu có
            if ($service->banner && Storage::disk('public')->exists($service->banner)) {
                Storage::disk('public')->delete($service->banner);
            }

            $bannerPath = $request->file('banner')->store('boosting/banners', 'public');
            $service->banner = $bannerPath;
        }

        // Cập nhật các trường khác
        $service->name = $request->name;
        $service->game_id = $request->game_id;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->sale_price = $request->sale_price;
        $service->estimated_days = $request->estimated_days;
        $service->requirements = $request->requirements;
        $service->includes = $request->includes;
        $service->is_active = $request->is_active ?? true;
        $service->save();

        return redirect()->route('admin.boosting.index')
            ->with('success', 'Dịch vụ cày thuê đã được cập nhật thành công.');
    }

    /**
     * Xóa dịch vụ cày thuê
     */
    public function destroy($id)
    {
        $service = BoostingService::findOrFail($id);

        // Xóa các ảnh liên quan
        if ($service->thumbnail && Storage::disk('public')->exists($service->thumbnail)) {
            Storage::disk('public')->delete($service->thumbnail);
        }

        if ($service->banner && Storage::disk('public')->exists($service->banner)) {
            Storage::disk('public')->delete($service->banner);
        }

        $service->delete();

        return redirect()->route('admin.boosting.index')
            ->with('success', 'Dịch vụ cày thuê đã được xóa thành công.');
    }
}
