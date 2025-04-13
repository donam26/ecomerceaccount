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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class GameServiceController extends Controller
{
    /**
     * Hiển thị danh sách dịch vụ
     */
    public function index(Request $request)
    {
        try {
            $query = GameService::query();
            
            // Thêm điều kiện status khi đã tồn tại cột
            if (Schema::hasColumn('game_services', 'status')) {
                $query->where('status', 'active');
            }
            
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
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@index: ' . $e->getMessage());
            $services = collect();
            return view('services.index', compact('services'))->with('error', 'Có lỗi xảy ra khi tải dịch vụ.');
        }
    }

    /**
     * Hiển thị chi tiết dịch vụ
     */
    public function show($slug)
    {
        try {
            $query = GameService::where('slug', $slug);
            
            // Thêm điều kiện status khi đã tồn tại cột
            if (Schema::hasColumn('game_services', 'status')) {
                $query->where('status', 'active');
            }
            
            $service = $query->firstOrFail();
            
            $packagesQuery = $service->packages();
            
            // Thêm điều kiện status cho packages khi đã tồn tại cột
            if (Schema::hasColumn('game_service_packages', 'status')) {
                $packagesQuery->where('status', 'active');
            }
            
            $packages = $packagesQuery->orderBy('display_order')->get();
            
            return view('services.show', compact('service', 'packages'));
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@show: ' . $e->getMessage());
            return redirect()->route('services.index')->with('error', 'Không tìm thấy dịch vụ hoặc đã xảy ra lỗi.');
        }
    }

    /**
     * Xử lý đặt dịch vụ
     */
    public function order(Request $request, $slug)
    {
        try {
            $query = GameService::where('slug', $slug);
            
            // Thêm điều kiện status khi đã tồn tại cột
            if (Schema::hasColumn('game_services', 'status')) {
                $query->where('status', 'active');
            }
            
            $service = $query->firstOrFail();
            
            // Xác định quy tắc xác thực dựa trên login_type
            $validationRules = [
                'package_id' => 'required|exists:game_service_packages,id',
                'notes' => 'nullable|string|max:500',
            ];
            
            // Thêm quy tắc xác thực dựa trên login_type của dịch vụ
            if ($service->login_type === 'username_password' || $service->login_type === 'both') {
                $validationRules['game_username'] = 'required|string|max:100';
                $validationRules['game_password'] = 'required|string|max:100';
            }
            
            if ($service->login_type === 'game_id' || $service->login_type === 'both') {
                $validationRules['game_id'] = 'required|string|max:100';
            }
            
            // Thêm trường game_server
            $validationRules['game_server'] = 'required|string|max:50';
            
            $validated = $request->validate($validationRules);
            
            // Tìm gói dịch vụ
            $package = ServicePackage::findOrFail($validated['package_id']);
            
            // Đảm bảo gói thuộc về dịch vụ này
            if ($package->game_service_id != $service->id) {
                return back()->withErrors(['package_id' => 'Gói dịch vụ không hợp lệ']);
            }
            
            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'user_id' => Auth::id(),
                'game_service_id' => $service->id,
                'game_service_package_id' => $package->id,
                'order_number' => ServiceOrder::generateOrderNumber(),
                'game_server' => $validated['game_server'],
                'notes' => $validated['notes'] ?? null,
                'amount' => $package->getDisplayPriceAttribute(),
            ];
            
            // Thêm thông tin đăng nhập dựa trên login_type
            if ($service->login_type === 'username_password' || $service->login_type === 'both') {
                $orderData['game_username'] = $validated['game_username'];
                $orderData['game_password'] = $validated['game_password'];
            }
            
            // Thêm game_id nếu được yêu cầu
            if ($service->login_type === 'game_id' || $service->login_type === 'both') {
                $orderData['game_id'] = $validated['game_id'];
            }
            
            // Tạo đơn hàng
            $order = ServiceOrder::create($orderData);
            
            // Chuyển hướng đến trang thanh toán
            return redirect()->route('payment.checkout', $order->order_number);
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@order: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Đã xảy ra lỗi khi đặt hàng. Vui lòng thử lại sau.']);
        }
    }
    
    /**
     * Hiển thị danh sách đơn hàng của người dùng
     */
    public function myOrders()
    {
        try {
            $orders = ServiceOrder::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            return view('services.my_orders', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@myOrders: ' . $e->getMessage());
            return view('services.my_orders', ['orders' => collect()])->with('error', 'Có lỗi xảy ra khi tải đơn hàng.');
        }
    }
    
    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function viewOrder($orderNumber)
    {
        try {
            $order = ServiceOrder::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            return view('services.order_detail', compact('order'));
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@viewOrder: ' . $e->getMessage());
            return redirect()->route('services.my_orders')->with('error', 'Không tìm thấy đơn hàng hoặc đã xảy ra lỗi.');
        }
    }

    /**
     * Xử lý đặt hàng với gói dịch vụ cụ thể
     */
    public function orderPackage(Request $request, $slug, $packageId)
    {
        try {
            $query = GameService::where('slug', $slug);
            
            // Thêm điều kiện status khi đã tồn tại cột
            if (Schema::hasColumn('game_services', 'status')) {
                $query->where('status', 'active');
            }
            
            $service = $query->firstOrFail();
            
            $package = ServicePackage::findOrFail($packageId);
            
            // Đảm bảo gói thuộc về dịch vụ này
            if ($package->game_service_id != $service->id) {
                return back()->withErrors(['package_id' => 'Gói dịch vụ không hợp lệ']);
            }
            
            // Đưa thông tin gói dịch vụ vào session để dùng trong form đặt hàng
            session()->flash('selected_package', $package);
            
            return view('services.order_form', compact('service', 'package'));
        } catch (\Exception $e) {
            Log::error('Lỗi trong GameServiceController@orderPackage: ' . $e->getMessage());
            return redirect()->route('services.index')->with('error', 'Đã xảy ra lỗi khi chọn gói dịch vụ.');
        }
    }
}

