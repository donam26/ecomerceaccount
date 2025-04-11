@extends('layouts.app')

@section('title', 'Đặt ' . $service->name . ' - ' . $package->name)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6">
        <!-- Breadcrumbs -->
        <nav class="mb-8">
            <ol class="flex text-sm text-gray-500 flex-wrap">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Trang chủ</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('services.index') }}" class="hover:text-blue-600 transition">Dịch vụ thuê câu cá</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('services.show', $service->slug) }}" class="hover:text-blue-600 transition">{{ $service->name }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
                <li class="text-gray-900 font-medium">Đặt hàng gói {{ $package->name }}</li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Thông tin đặt hàng -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm p-8 mb-6 hover:shadow-md transition duration-300">
                    <h1 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Đặt hàng thuê câu cá
                    </h1>
                    
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Bạn đã chọn gói dịch vụ</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p><span class="font-semibold">{{ $service->name }} - {{ $package->name }}</span></p>
                                    <p class="mt-1">Vui lòng điền thông tin tài khoản game của bạn để chúng tôi có thể thực hiện dịch vụ.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('services.order', $service->slug) }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="game_username" class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập game <span class="text-red-500">*</span></label>
                                <input type="text" id="game_username" name="game_username" value="{{ old('game_username') }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('game_username') border-red-500 @enderror" required>
                                @error('game_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="game_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu game <span class="text-red-500">*</span></label>
                                <input type="password" id="game_password" name="game_password" value="{{ old('game_password') }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('game_password') border-red-500 @enderror" required>
                                @error('game_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Thông tin của bạn được mã hóa và bảo mật</p>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="game_server" class="block text-sm font-medium text-gray-700 mb-1">Máy chủ/Server <span class="text-red-500">*</span></label>
                            <input type="text" id="game_server" name="game_server" value="{{ old('game_server') }}" placeholder="VD: Asia, NA, EU..." class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('game_server') border-red-500 @enderror" required>
                            @error('game_server')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú thêm</label>
                            <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Các yêu cầu đặc biệt hoặc thông tin bổ sung</p>
                        </div>
                        
                        <div class="flex items-center space-x-2 mt-8">
                            <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                            <label for="terms" class="text-sm text-gray-700">
                                Tôi đồng ý với <a href="#" class="text-blue-600 hover:text-blue-800">điều khoản dịch vụ</a> và <a href="#" class="text-blue-600 hover:text-blue-800">chính sách bảo mật</a>
                            </label>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-center text-sm font-medium">
                                Tiếp tục thanh toán
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Thông tin gói dịch vụ đã chọn -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-8 mb-6 hover:shadow-md transition duration-300 sticky top-24">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Thông tin đơn hàng
                    </h2>
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Dịch vụ:</span>
                            <span class="font-medium text-gray-800">{{ $service->name }}</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Gói:</span>
                            <span class="font-semibold text-gray-800">{{ $package->name }}</span>
                        </div>
                        @if($package->description)
                        <div class="mt-2 text-sm text-gray-600">
                            {{ $package->description }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Giá gói:</span>
                            @if($package->sale_price && $package->sale_price < $package->price)
                            <div class="text-right">
                                <span class="line-through text-gray-500 text-sm">{{ number_format($package->price, 0, ',', '.') }}đ</span>
                                <span class="block font-bold text-red-600">{{ number_format($package->sale_price, 0, ',', '.') }}đ</span>
                            </div>
                            @else
                            <span class="font-bold text-gray-800">{{ number_format($package->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Thời gian hoàn thành:</span>
                            <span class="font-medium text-gray-800">{{ $service->metadata['estimated_days'] ?? 3 }} ngày</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span class="text-gray-800">Tổng thanh toán:</span>
                        <span class="text-blue-600">{{ number_format($package->getDisplayPriceAttribute(), 0, ',', '.') }}đ</span>
                    </div>
                    
                    <div class="mt-6 text-xs text-gray-500">
                        <p>Thanh toán an toàn qua ví điện tử hoặc thẻ ngân hàng.</p>
                        <p class="mt-1">Bạn sẽ được chuyển đến trang thanh toán sau khi hoàn tất đơn hàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 