<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameService;
use App\Models\ServicePackage;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ
     */
    public function index(Request $request)
    {
        $query = GameService::where('status', 'active');
        
        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sắp xếp
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $services = $query->paginate(12);
        return view('services.index', compact('services'));
    }

    /**
     * Hiển thị chi tiết dịch vụ
     */
    public function show($slug)
    {
        $service = GameService::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        $packages = $service->packages()
            ->where('status', 'active')
            ->orderBy('display_order')
            ->get();
        
        return view('services.show', compact('service', 'packages'));
    }

    /**
     * Xử lý đặt dịch vụ
     */
    public function order(Request $request, $slug)
    {
        $service = GameService::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        $validationRules = [
            'game_username' => 'required|string|max:100',
            'game_password' => 'required|string|max:100',
            'game_server' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
            'package_id' => 'required|exists:game_service_packages,id', // Luôn yêu cầu package_id
        ];
        
        $validated = $request->validate($validationRules);
        
        // Tìm gói dịch vụ
        $package = ServicePackage::findOrFail($validated['package_id']);
        
        // Đảm bảo gói thuộc về dịch vụ này
        if ($package->game_service_id != $service->id) {
            return back()->withErrors(['package_id' => 'Gói dịch vụ không hợp lệ']);
        }
        
        // Tạo đơn hàng
        $orderData = [
            'user_id' => Auth::id(),
            'game_service_id' => $service->id,
            'service_package_id' => $package->id,
            'order_number' => ServiceOrder::generateOrderNumber(),
            'game_username' => $validated['game_username'],
            'game_password' => $validated['game_password'],
            'game_server' => $validated['game_server'],
            'notes' => $validated['notes'] ?? null,
            'amount' => $package->getDisplayPriceAttribute(),
        ];
        
        $order = ServiceOrder::create($orderData);
        
        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.checkout', $order->order_number);
    }
    
    /**
     * Hiển thị danh sách đơn hàng của người dùng
     */
    public function myOrders()
    {
        $orders = ServiceOrder::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('services.my_orders', compact('orders'));
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function orderDetail($orderNumber)
    {
        $order = ServiceOrder::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('services.order_detail', compact('order'));
    }

    /**
     * Xử lý đặt hàng với gói dịch vụ cụ thể
     */
    public function orderPackage(Request $request, $slug, $packageId)
    {
        $service = GameService::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        $package = ServicePackage::findOrFail($packageId);
        
        // Đảm bảo gói thuộc về dịch vụ này
        if ($package->game_service_id != $service->id) {
            return back()->withErrors(['package_id' => 'Gói dịch vụ không hợp lệ']);
        }
        
        // Đưa thông tin gói dịch vụ vào session để dùng trong form đặt hàng
        session()->flash('selected_package', $package);
        
        return view('services.order_form', compact('service', 'package'));
    }
}

        
        // Đảm bảo gói thuộc về dịch vụ này
        if ($package->game_service_id != $service->id) {
            return back()->withErrors(['package_id' => 'Gói dịch vụ không hợp lệ']);
        }
        
        // Đưa thông tin gói dịch vụ vào session để dùng trong form đặt hàng
        session()->flash('selected_package', $package);
        
        return view('services.order_form', compact('service', 'package'));
    }
}

use Illuminate\Support\Facades\DB;

class GameServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ
     */
    public function index(Request $request)
    {
        $query = GameService::where('status', 'active');
        
        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sắp xếp
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'name-asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price-asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $services = $query->paginate(12);
        return view('services.index', compact('services'));
    }

    /**
     * Hiển thị chi tiết dịch vụ
     */
    public function show($slug)
    {
        $service = GameService::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        $packages = $service->packages()
            ->where('status', 'active')
            ->orderBy('display_order')
            ->get();
        
        return view('services.show', compact('service', 'packages'));
    }

    /**
     * Xử lý đặt dịch vụ
     */
    public function order(Request $request, $slug)
    {
        $service = GameService::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
            
        $validationRules = [
            'game_username' => 'required|string|max:100',
            'game_password' => 'required|string|max:100',
            'game_server' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ];
        
        // Nếu dịch vụ có packages, yêu cầu package_id
        if ($service->packages()->count() > 0) {
            $validationRules['package_id'] = 'required|exists:service_packages,id';
        }
        
        $validated = $request->validate($validationRules);
        
        // Tạo đơn hàng
        $orderData = [
            'user_id' => Auth::id(),
            'game_service_id' => $service->id,
            'order_number' => ServiceOrder::generateOrderNumber(),
            'game_username' => $validated['game_username'],
            'game_password' => $validated['game_password'],
            'game_server' => $validated['game_server'],
            'notes' => $validated['notes'] ?? null,
        ];
        
        // Tính toán giá tiền
        if (isset($validated['package_id'])) {
            $package = ServicePackage::findOrFail($validated['package_id']);
            $orderData['service_package_id'] = $package->id;
            $orderData['amount'] = $package->getDisplayPriceAttribute();
        } else {
            $orderData['amount'] = $service->getDisplayPriceAttribute();
        }
        
        $order = ServiceOrder::create($orderData);
        
        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.checkout', $order->order_number);
    }
    
    /**
     * Hiển thị danh sách đơn hàng của người dùng
     */
    public function myOrders()
    {
        $orders = ServiceOrder::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('services.my_orders', compact('orders'));
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function orderDetail($orderNumber)
    {
        $order = ServiceOrder::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();
            
        return view('services.order_detail', compact('order'));
    }
}
