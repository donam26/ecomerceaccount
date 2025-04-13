@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Đơn hàng của tôi</h1>
        
        @if($orders->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                </svg>
                <p class="text-xl text-gray-600 mb-4">Bạn chưa có đơn hàng nào</p>
                <a href="{{ route('accounts.index') }}" class="btn-primary inline-block">Mua tài khoản ngay</a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Mã đơn hàng</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tài khoản</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Số tiền</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ngày đặt</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $order->account->title }}
                                        <div class="text-xs text-gray-500">{{ $order->account->game->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ number_format($order->amount, 0, ',', '.') }}đ</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($order->status == 'pending')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Chờ thanh toán</span>
                                        @elseif($order->status == 'completed')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Đã hoàn thành</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Đã hủy</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if(strpos($order->order_number, 'SRV-') === 0)
                                            <a href="{{ route('services.view_order', $order->order_number) }}" class="text-blue-600 hover:text-blue-800 mr-3">Chi tiết</a>
                                        @elseif(strpos($order->order_number, 'BST-') === 0)
                                            <a href="{{ route('boosting.orders.show', $order->order_number) }}" class="text-blue-600 hover:text-blue-800 mr-3">Chi tiết</a>
                                        @else
                                            <a href="{{ route('orders.show', $order->order_number) }}" class="text-blue-600 hover:text-blue-800 mr-3">Chi tiết</a>
                                        @endif
                                        
                                        @if($order->status == 'pending')
                                            <a href="{{ route('payment.checkout', $order->order_number) }}" class="text-green-600 hover:text-green-800 mr-3">Thanh toán</a>
                                            
                                            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">Hủy</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Phân trang -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 