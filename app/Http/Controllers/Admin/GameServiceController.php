<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameService;
use App\Models\ServicePackage;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ
     */
    public function index()
    {
        $services = GameService::with('game')->orderBy('created_at', 'desc')->paginate(20);
        $games = Game::where('is_active', 1)->get();
        return view('admin.services.index', compact('services', 'games'));
    }
    
    /**
     * Hiển thị form tạo dịch vụ mới
     */
    public function create()
    {
        $games = Game::where('is_active', 1)->get();
        return view('admin.services.create', compact('games'));
    }
    
    /**
     * Lưu dịch vụ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
        ]);
        
        // Xử lý upload hình ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        }
        
        // Tạo slug từ tên
        $validated['slug'] = Str::slug($validated['name']);
        
        // Debug: log dữ liệu trước khi tạo
        \Log::info('Creating new GameService with data: ', $validated);
        
        // Tạo dịch vụ
        GameService::create($validated);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được tạo thành công');
    }
    
    /**
     * Hiển thị form chỉnh sửa dịch vụ
     */
    public function edit($id)
    {
        $service = GameService::findOrFail($id);
        $games = Game::where('is_active', 1)->get();
        return view('admin.services.edit', compact('service', 'games'));
    }
    
    /**
     * Cập nhật dịch vụ
     */
    public function update(Request $request, $id)
    {
        $service = GameService::findOrFail($id);
        
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
        ]);
        
        // Xử lý upload hình ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        }
        
        // Cập nhật slug nếu tên thay đổi
        if ($service->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Cập nhật dịch vụ
        $service->update($validated);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được cập nhật thành công');
    }
    
    /**
     * Xóa dịch vụ
     */
    public function destroy($id)
    {
        $service = GameService::findOrFail($id);
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Dịch vụ đã được xóa thành công');
    }
    
    /**
     * Hiển thị danh sách gói dịch vụ
     */
    public function packages($serviceId)
    {
        $service = GameService::findOrFail($serviceId);
        $packages = $service->packages()->orderBy('display_order')->get();
        
        return view('admin.services.packages.index', compact('service', 'packages'));
    }
    
    /**
     * Hiển thị form tạo gói dịch vụ
     */
    public function createPackage($serviceId)
    {
        $service = GameService::findOrFail($serviceId);
        return view('admin.services.packages.create', compact('service'));
    }
    
    /**
     * Lưu gói dịch vụ mới
     */
    public function storePackage(Request $request, $serviceId)
    {
        $service = GameService::findOrFail($serviceId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'display_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // Chỉ lưu sale_price khi nó được gửi đến và có giá trị
        if (empty($validated['sale_price'])) {
            $validated['sale_price'] = null;
        }
        
        // Xử lý upload hình ảnh
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('service_packages', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        }
        
        // Tạo gói dịch vụ
        $service->packages()->create($validated);
        
        return redirect()->route('admin.services.packages', $service->id)
            ->with('success', 'Gói dịch vụ đã được tạo thành công');
    }
    
    /**
     * Hiển thị form chỉnh sửa gói dịch vụ
     */
    public function editPackage($serviceId, $packageId)
    {
        $service = GameService::findOrFail($serviceId);
        $package = ServicePackage::findOrFail($packageId);
        
        return view('admin.services.packages.edit', compact('service', 'package'));
    }
    
    /**
     * Cập nhật gói dịch vụ
     */
    public function updatePackage(Request $request, $serviceId, $packageId)
    {
        $service = GameService::findOrFail($serviceId);
        $package = ServicePackage::findOrFail($packageId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'display_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);
        
        // Chỉ lưu sale_price khi nó được gửi đến và có giá trị
        if (empty($validated['sale_price'])) {
            $validated['sale_price'] = null;
        }
        
        // Xử lý xóa ảnh hiện tại
        if ($request->has('remove_image') && $request->remove_image == '1') {
            // Xóa file ảnh cũ nếu cần
            if ($package->image && file_exists(public_path(str_replace('/storage/', '/app/public/', $package->image)))) {
                unlink(public_path(str_replace('/storage/', '/app/public/', $package->image)));
            }
            $validated['image'] = null;
        }
        // Xử lý upload ảnh mới
        elseif ($request->hasFile('image')) {
            // Xóa file ảnh cũ nếu có
            if ($package->image && file_exists(public_path(str_replace('/storage/', '/app/public/', $package->image)))) {
                unlink(public_path(str_replace('/storage/', '/app/public/', $package->image)));
            }
            $imagePath = $request->file('image')->store('service_packages', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        }
        
        // Loại bỏ trường remove_image khỏi dữ liệu cập nhật
        unset($validated['remove_image']);
        
        // Cập nhật gói dịch vụ
        $package->update($validated);
        
        return redirect()->route('admin.services.packages', $service->id)
            ->with('success', 'Gói dịch vụ đã được cập nhật thành công');
    }
    
    /**
     * Xóa gói dịch vụ
     */
    public function destroyPackage($serviceId, $packageId)
    {
        $package = ServicePackage::findOrFail($packageId);
        $package->delete();
        
        return redirect()->route('admin.services.packages', $serviceId)
            ->with('success', 'Gói dịch vụ đã được xóa thành công');
    }
    
    /**
     * Hiển thị danh sách đơn hàng dịch vụ
     */
    public function orders(Request $request)
    {
        $query = ServiceOrder::with(['user', 'service', 'package']);
        
        // Lọc theo dịch vụ
        if ($request->has('service') && $request->service) {
            $query->where('game_service_id', $request->service);
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo ngày
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('game_username', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $services = GameService::where('status', 'active')->get();
        
        return view('admin.services.orders.index', compact('orders', 'services'));
    }
    
    /**
     * Hiển thị chi tiết đơn hàng dịch vụ
     */
    public function showOrder($id)
    {
        $order = ServiceOrder::with(['user', 'service', 'package'])->findOrFail($id);
        return view('admin.services.orders.show', compact('order'));
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = ServiceOrder::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        $newStatus = $request->status;
        $order->status = $newStatus;
        
        // Cập nhật ghi chú nếu có
        if ($request->filled('notes')) {
            $orderNotes = $order->notes ?? '';
            $orderNotes .= "\n" . now()->format('d/m/Y H:i') . " - " . auth()->user()->name . ": " . $request->notes;
            $order->notes = $orderNotes;
        }
        
        // Nếu trạng thái là hoàn thành, cập nhật thời gian hoàn thành
        if ($newStatus == 'completed' && !$order->completed_at) {
            $order->completed_at = now();
        }
        
        // Nếu trạng thái là hủy, cập nhật thời gian hủy
        if ($newStatus == 'cancelled' && !$order->cancelled_at) {
            $order->cancelled_at = now();
        }
        
        $order->save();
        
        return redirect()->route('admin.services.orders.show', $order->id)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công');
    }
}
