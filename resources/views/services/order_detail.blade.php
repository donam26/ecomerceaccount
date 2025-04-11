@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('services.my_orders') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Quay lại danh sách đơn hàng
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-wrap justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Chi tiết đơn hàng #{{ $order->order_number }}
                    </h1>
                    
                    <div class="mt-2 sm:mt-0">
                        @if($order->status == 'pending')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Chờ thanh toán
                        </span>
                        @elseif($order->status == 'paid')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Đã thanh toán
                        </span>
                        @elseif($order->status == 'processing')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            Đang xử lý
                        </span>
                        @elseif($order->status == 'completed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Hoàn thành
                        </span>
                        @elseif($order->status == 'cancelled')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Đã hủy
                        </span>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Ngày đặt hàng</p>
                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phương thức thanh toán</p>
                        <p class="font-medium">
                            @if($order->payment_method == 'wallet')
                            Ví điện tử
                            @elseif($order->payment_method == 'bank_transfer')
                            Chuyển khoản ngân hàng
                            @elseif($order->payment_method == 'momo')
                            Ví MoMo
                            @elseif($order->payment_method == 'vnpay')
                            VNPAY
                            @else
                            {{ $order->payment_method ?? 'Chưa thanh toán' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tổng tiền</p>
                        <p class="font-bold text-lg">{{ number_format($order->amount, 0, ',', '.') }}đ</p>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin dịch vụ -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Thông tin dịch vụ</h2>
                
                <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-1/4">
                        <img src="{{ $order->service->image ? asset($order->service->image) : asset('images/default-service.jpg') }}" 
                            alt="{{ $order->service->name }}" 
                            class="w-full h-48 object-cover rounded-lg">
                    </div>
                    <div class="w-full md:w-3/4 md:pl-6 mt-4 md:mt-0">
                        <h3 class="text-lg font-semibold">{{ $order->service->name }}</h3>
                        
                        @if($order->package)
                        <div class="mt-2 inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            Gói: {{ $order->package->name }}
                        </div>
                        @endif
                        
                        <div class="mt-4 text-sm">
                            <p class="text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit($order->service->description, 300) }}</p>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm">Giá dịch vụ</p>
                                <p class="font-medium">{{ number_format($order->amount, 0, ',', '.') }}đ</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Thời gian ước tính</p>
                                <p class="font-medium">{{ $order->service->metadata['estimated_days'] ?? 3 }} ngày</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin tài khoản -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Thông tin tài khoản game</h2>
                
                @if($order->isPaid())
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Tên đăng nhập</p>
                            <p class="font-medium">{{ $order->game_username }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Mật khẩu</p>
                            <p class="font-medium">{{ str_repeat('●', 8) }}</p>
                        </div>
                    </div>
                    
                    @if($order->game_server)
                    <div class="mt-4">
                        <p class="text-gray-500 text-sm">Server game</p>
                        <p class="font-medium">{{ $order->game_server }}</p>
                    </div>
                    @endif
                    
                    @if($order->notes)
                    <div class="mt-4">
                        <p class="text-gray-500 text-sm">Ghi chú</p>
                        <p class="font-medium">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
                @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-yellow-700">Thông tin tài khoản game sẽ được hiển thị sau khi đơn hàng được thanh toán.</p>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Tiến trình đơn hàng -->
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-6">Tiến trình đơn hàng</h2>
                
                <div class="relative">
                    <!-- Đường kẻ kết nối vertical -->
                    <div class="absolute h-full w-0.5 bg-gray-200 left-1.5 top-1.5"></div>
                    
                    <ul class="space-y-6">
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-blue-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đã được tạo</h4>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        
                        @if($order->isPaid())
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-blue-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đã được thanh toán</h4>
                                <span class="text-xs text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->status == 'processing')
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-blue-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đang được xử lý</h4>
                                <span class="text-xs text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->status == 'completed')
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-blue-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đang được xử lý</h4>
                                <span class="text-xs text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-green-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đã hoàn thành</h4>
                                <span class="text-xs text-gray-500">{{ $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->status == 'cancelled')
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-red-600 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm">Đơn hàng đã bị hủy</h4>
                                <span class="text-xs text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        @endif
                        
                        @if($order->status == 'pending')
                        <li class="relative pl-8">
                            <span class="absolute left-0 top-1.5 flex h-4 w-4 rounded-full bg-gray-300 items-center justify-center">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                            <div class="flex flex-col">
                                <h4 class="font-semibold text-sm text-gray-500">Đang chờ thanh toán</h4>
                                <a href="{{ route('payment.checkout', $order->order_number) }}" class="text-blue-600 hover:text-blue-800 text-sm mt-1">
                                    Thanh toán ngay
                                </a>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        
        @if($order->status == 'pending')
        <div class="flex justify-center mt-8">
            <a href="{{ route('payment.checkout', $order->order_number) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Thanh toán đơn hàng
            </a>
        </div>
        @endif
    </div>
</div>
@endsection 