@extends('layouts.app')

@section('title', 'Đơn hàng dịch vụ câu cá bóng 5 6 của tôi')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Đơn hàng dịch vụ câu cá bóng 5 6 của tôi</h1>
        
        <!-- Bộ lọc -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form action="{{ route('services.my_orders') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Thời gian</label>
                    <select id="date" name="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>Tháng này</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Mã đơn hàng..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full">
                        Lọc kết quả
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Danh sách đơn hàng -->
        <div class="bg-white shadow overflow-hidden rounded-lg">
            @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã đơn hàng
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dịch vụ
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tổng tiền
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày đặt
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $order->service->name }}</div>
                                @if($order->package)
                                <div class="text-xs text-gray-500">Gói: {{ $order->package->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($order->amount, 0, ',', '.') }}đ</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->status == 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Chờ thanh toán
                                </span>
                                @elseif($order->status == 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Đã thanh toán
                                </span>
                                @elseif($order->status == 'processing')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Đang xử lý
                                </span>
                                @elseif($order->status == 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Hoàn thành
                                </span>
                                @elseif($order->status == 'cancelled')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Đã hủy
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('services.view_order', $order->order_number) }}" class="text-blue-600 hover:text-blue-900">Xem chi tiết</a>
                                
                                @if($order->status == 'pending')
                                <a href="{{ route('payment.checkout', $order->order_number) }}" class="ml-3 text-green-600 hover:text-green-900">Thanh toán</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
            @else
            <div class="py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-xl font-medium text-gray-900">Không có đơn hàng nào</h3>
                <p class="mt-1 text-gray-500">Bạn chưa có đơn hàng dịch vụ câu cá bóng 5 6 nào.</p>
                <div class="mt-6">
                    <a href="{{ route('services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                        Xem dịch vụ câu cá bóng 5 6
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 