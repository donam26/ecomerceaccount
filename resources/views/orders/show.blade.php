@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Quay lại
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Chi tiết đơn hàng #{{ $order->order_number }}</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Thông tin đơn hàng</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 mb-2">Trạng thái:</p>
                    <p class="font-medium">
                        @if($order->status == 'pending')
                            <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Đang chờ thanh toán</span>
                        @elseif($order->status == 'completed')
                            <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded">Đã thanh toán</span>
                        @elseif($order->status == 'cancelled')
                            <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded">Đã hủy</span>
                        @else
                            <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ $order->status }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2">Ngày đặt hàng:</p>
                    <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2">Tổng tiền:</p>
                    <p class="font-medium text-lg text-blue-700">{{ number_format($order->amount) }}đ</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-2">Phương thức thanh toán:</p>
                    <p class="font-medium">{{ $order->payment_method ?? 'Chuyển khoản ngân hàng' }}</p>
                </div>
                @if($order->completed_at)
                <div>
                    <p class="text-gray-600 mb-2">Ngày thanh toán:</p>
                    <p class="font-medium">{{ $order->completed_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Thông tin tài khoản đã mua</h2>
        </div>
        <div class="p-6">
            @if($order->status == 'completed' && $accountInfo)
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <div class="flex items-center mb-2">
                        <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium text-blue-800">Vui lòng lưu trữ thông tin tài khoản này ở nơi an toàn!</span>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 mb-1">Tên game:</p>
                            <p class="font-medium">{{ $accountInfo['game_name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Tên đăng nhập:</p>
                            <div class="flex items-center">
                                <p class="font-medium" id="username">{{ $accountInfo['username'] }}</p>
                                <button type="button" onclick="copyToClipboard('username')" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Mật khẩu:</p>
                            <div class="flex items-center">
                                <p class="font-medium" id="password">{{ $accountInfo['password'] }}</p>
                                <button type="button" onclick="copyToClipboard('password')" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            @elseif($order->status == 'completed')
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="font-medium text-yellow-800">Thông tin tài khoản đang được cập nhật. Vui lòng liên hệ bộ phận hỗ trợ nếu bạn không nhận được thông tin trong vòng 15 phút.</span>
                    </div>
                </div>
            @elseif($order->status == 'pending')
                <div class="text-center py-4">
                    <p class="text-gray-600 mb-4">Vui lòng hoàn tất thanh toán để nhận thông tin tài khoản.</p>
                    <a href="{{ route('payment.checkout', $order->order_number) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Thanh toán ngay
                    </a>
                </div>
            @else
                <p class="text-gray-600 py-4 text-center">Đơn hàng đã bị hủy hoặc không thể xử lý.</p>
            @endif
        </div>
    </div>

    @if($order->status == 'pending')
    <div class="text-center mt-6">
        <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                Hủy đơn hàng
            </button>
        </form>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.innerText;
        
        navigator.clipboard.writeText(text).then(() => {
            // Hiển thị thông báo đã sao chép
            const originalText = element.innerHTML;
            element.innerHTML = '<span class="text-green-600">Đã sao chép!</span>';
            
            setTimeout(() => {
                element.innerHTML = originalText;
            }, 1500);
        }, (err) => {
            console.error('Không thể sao chép: ', err);
        });
    }
</script>
@endsection 