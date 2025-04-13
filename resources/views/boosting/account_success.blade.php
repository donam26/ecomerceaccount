@extends('layouts.app')

@section('title', 'Xác nhận thành công')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex flex-col items-center justify-center mb-8">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2 text-center">Thông tin đã được xác nhận!</h1>
                <p class="text-gray-600 text-center mb-6">
                    Cảm ơn bạn đã cung cấp thông tin tài khoản game. Đội ngũ chúng tôi sẽ bắt đầu thực hiện dịch vụ cày thuê ngay lập tức.
                </p>
                
                <div class="w-full bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                    <p class="font-bold">Thông tin đơn hàng của bạn:</p>
                    <ul class="mt-2 space-y-1">
                        <li><strong>Mã đơn hàng:</strong> {{ $order->order_number }}</li>
                        <li><strong>Dịch vụ:</strong> {{ $order->service->name }}</li>
                        <li><strong>Game:</strong> {{ $order->service->game->name }}</li>
                        <li><strong>Thời gian hoàn thành dự kiến:</strong> {{ $order->service->estimated_days }} ngày</li>
                    </ul>
                </div>
            </div>
            
            <div class="grid gap-4 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Các bước tiếp theo</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
                                <span class="font-bold">1</span>
                            </div>
                            <h3 class="font-semibold text-gray-900">Xử lý đơn hàng</h3>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Đơn hàng của bạn đang được xử lý. Nhân viên của chúng tôi sẽ bắt đầu thực hiện dịch vụ.
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
                                <span class="font-bold">2</span>
                            </div>
                            <h3 class="font-semibold text-gray-900">Cập nhật tiến độ</h3>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Bạn sẽ nhận được thông báo về tiến độ thực hiện dịch vụ qua email.
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
                                <span class="font-bold">3</span>
                            </div>
                            <h3 class="font-semibold text-gray-900">Hoàn thành</h3>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Chúng tôi sẽ thông báo khi dịch vụ hoàn thành và bạn có thể đăng nhập vào tài khoản game của mình.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col space-y-4">
                <a href="{{ route('boosting.orders.show', $order->order_number) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Xem chi tiết đơn hàng
                </a>
                
                <a href="{{ route('home') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Quay về trang chủ
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Cần hỗ trợ?</h2>
            <p class="text-gray-600 mb-4">
                Nếu bạn có bất kỳ câu hỏi nào về đơn hàng hoặc cần hỗ trợ thêm, vui lòng liên hệ với chúng tôi qua:
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-gray-700">support@gameshop.com</span>
                </div>
                
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="text-gray-700">0876085633</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 