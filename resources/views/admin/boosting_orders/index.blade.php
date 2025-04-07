@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng cày thuê')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Quản lý đơn hàng cày thuê</h1>
        </div>

        <!-- Thống kê -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Tất cả</h4>
                <p class="text-2xl font-bold text-gray-700">{{ array_sum($stats) }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Chờ thanh toán</h4>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Đã thanh toán</h4>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['paid'] ?? 0 }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Đang xử lý</h4>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['processing'] ?? 0 }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Hoàn thành</h4>
                <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</p>
            </div>
        </div>
        
        <!-- Thông báo -->
        @if(session('success'))
        <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif
        
        <!-- Bộ lọc -->
        <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md">
            <form action="{{ route('admin.boosting_orders.index') }}" method="GET" class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                        <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700">Sắp xếp theo</label>
                        <select id="sort" name="sort" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày đặt</option>
                            <option value="amount" {{ request('sort') == 'amount' ? 'selected' : '' }}>Giá trị</option>
                            <option value="order_number" {{ request('sort') == 'order_number' ? 'selected' : '' }}>Mã đơn hàng</option>
                        </select>
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700">Thứ tự</label>
                        <select id="order" name="order" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <div class="w-full md:w-64">
                        <label for="search" class="block text-sm font-medium text-gray-700">Tìm kiếm</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                   placeholder="Nhập mã đơn hàng">
                        </div>
                    </div>
                    <div class="ml-4 mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Danh sách đơn hàng -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã đơn hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Khách hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dịch vụ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá trị
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thông tin tài khoản
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày đặt
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->user->name }}
                                <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="text-blue-600">{{ $order->service->name }}</span>
                                <div class="text-xs text-gray-400">{{ $order->service->game->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($order->amount, 0, ',', '.') }}đ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Chờ thanh toán
                                    </span>
                                @elseif($order->status == 'paid')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        Đã thanh toán
                                    </span>
                                @elseif($order->status == 'processing')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        Đang xử lý
                                    </span>
                                @elseif($order->status == 'completed')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Đã hoàn thành
                                    </span>
                                @elseif($order->status == 'cancelled')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        Đã hủy
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ $order->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($order->hasAccountInfo())
                                    <span class="text-green-600">Đã cung cấp</span>
                                @else
                                    <span class="text-red-600">Chưa cung cấp</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.boosting_orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Chi tiết
                                </a>
                                
                                @if($order->hasAccountInfo())
                                <a href="{{ route('admin.boosting_orders.account', $order->id) }}" class="text-green-600 hover:text-green-900">
                                    Tài khoản
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                Không có dữ liệu đơn hàng cày thuê.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 