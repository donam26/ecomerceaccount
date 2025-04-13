@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="bg-gray-50">
    <!-- Hero Banner -->
    <div class="relative">
        @if($service->image)
        <div class="w-full h-[400px] overflow-hidden">
            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
        </div>
        @else
        <div class="w-full h-[400px] bg-gradient-to-r from-blue-600 to-indigo-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
        </div>
        @endif
        
        <div class="absolute bottom-0 left-0 w-full p-8 text-white">
            <div class="container mx-auto max-w-7xl">
                <div class="flex flex-col space-y-3">
                    <div class="flex items-center space-x-3 flex-wrap">
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium shadow-sm mb-2">{{ $service->game->name }}</span>
                        <span class="flex items-center bg-blue-600/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $service->metadata['estimated_days'] ?? 3 }} ngày
                        </span>
                    </div>
                    <h1 class="text-4xl font-bold mb-2 text-shadow">{{ $service->name }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto max-w-7xl py-12 px-4 sm:px-6">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Thông tin chính -->
            <div class="w-full lg:w-2/3">
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
                        <li class="text-gray-900 font-medium">{{ $service->name }}</li>
                    </ol>
                </nav>

                <!-- Mô tả dịch vụ -->
                <div class="bg-white rounded-xl shadow-sm p-8 mb-6 hover:shadow-md transition duration-300">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mô tả dịch vụ
                    </h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! $service->description !!}
                    </div>
                </div>

                <!-- Các gói dịch vụ -->
                @if($packages && $packages->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-8 mb-6 hover:shadow-md transition duration-300">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        Các gói dịch vụ
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($packages as $package)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition duration-300 {{ $package->status === 'inactive' ? 'opacity-70' : '' }}">
                            @if($package->image)
                            <div class="w-full h-48 overflow-hidden">
                                <img src="{{ asset($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover transition-transform hover:scale-105">
                            </div>
                            @else
                            <div class="w-full h-48 bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            @endif
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $package->name }}</h3>
                                
                                @if($package->description)
                                <p class="text-gray-600 mb-4 text-sm">{{ $package->description }}</p>
                                @endif
                                
                                <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-4">
                                    <div>
                                        @if($package->sale_price && $package->sale_price < $package->price)
                                        <p class="text-gray-500 line-through text-sm">{{ number_format($package->price, 0, ',', '.') }}đ</p>
                                        <p class="text-xl font-bold text-red-600">{{ number_format($package->sale_price, 0, ',', '.') }}đ</p>
                                        @else
                                        <p class="text-xl font-bold text-gray-800">{{ number_format($package->price, 0, ',', '.') }}đ</p>
                                        @endif
                                    </div>
                                    
                                    @if($package->status === 'active')
                                    <form action="{{ route('services.order_package', [$service->slug, $package->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                                            Chọn gói này
                                        </button>
                                    </form>
                                    @else
                                    <span class="bg-gray-200 text-gray-600 py-2 px-4 rounded-md text-sm">
                                        Không khả dụng
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quy trình đặt hàng -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition duration-300">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        Quy trình đặt hàng
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="flex flex-col items-center text-center p-6 bg-blue-50 rounded-xl hover:bg-blue-100 transition duration-300">
                            <div class="bg-blue-600 text-white rounded-full p-4 mb-4 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-gray-800">1. Đặt dịch vụ</h3>
                            <p class="text-gray-600">Chọn dịch vụ và thanh toán qua cổng thanh toán an toàn của chúng tôi.</p>
                        </div>
                        <div class="flex flex-col items-center text-center p-6 bg-blue-50 rounded-xl hover:bg-blue-100 transition duration-300">
                            <div class="bg-blue-600 text-white rounded-full p-4 mb-4 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-gray-800">2. Xác nhận & Lên lịch</h3>
                            <p class="text-gray-600">Chúng tôi sẽ liên hệ để xác nhận và lên lịch thực hiện công việc.</p>
                        </div>
                        <div class="flex flex-col items-center text-center p-6 bg-blue-50 rounded-xl hover:bg-blue-100 transition duration-300">
                            <div class="bg-blue-600 text-white rounded-full p-4 mb-4 shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-gray-800">3. Hoàn thành</h3>
                            <p class="text-gray-600">Nhận thông báo khi hoàn thành và kiểm tra kết quả trước khi xác nhận.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đặt hàng -->
            <div class="w-full lg:w-1/3 mt-8 lg:mt-0">
                <div class="bg-white rounded-xl shadow p-8 sticky top-24 hover:shadow-lg transition duration-300">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Đặt hàng ngay
                    </h2>
                    
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Thời gian hoàn thành:</span>
                        <span class="font-semibold text-gray-800 bg-blue-50 px-3 py-1 rounded-full">{{ $service->metadata['estimated_days'] ?? 3 }} ngày</span>
                    </div>
                    
                    @if($packages && $packages->count() > 0)
                    <div class="mb-6">
                        <span class="text-gray-600 font-medium block mb-3">Chọn gói dịch vụ:</span>
                        <div class="space-y-3">
                            @foreach($packages as $package)
                            <div class="border border-gray-200 rounded-lg overflow-hidden hover:border-blue-300 transition-colors {{ $package->status === 'inactive' ? 'opacity-70' : '' }}">
                                <div class="flex items-start p-3">
                                    @if($package->image)
                                    <div class="w-16 h-16 rounded overflow-hidden mr-3 flex-shrink-0">
                                        <img src="{{ asset($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                    <div class="flex-grow">
                                        <h3 class="text-gray-800 font-medium">{{ $package->name }}</h3>
                                        @if($package->description)
                                        <p class="text-gray-600 text-sm mt-1">{{ $package->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right ml-3 flex-shrink-0">
                                        @if($package->sale_price && $package->sale_price < $package->price)
                                        <p class="text-gray-500 line-through text-sm">{{ number_format($package->price, 0, ',', '.') }}đ</p>
                                        <p class="font-bold text-red-600">{{ number_format($package->sale_price, 0, ',', '.') }}đ</p>
                                        @else
                                        <p class="font-bold text-gray-800">{{ number_format($package->price, 0, ',', '.') }}đ</p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($package->status === 'active')
                                <div class="bg-gray-50 p-3 text-right border-t border-gray-100">
                                    <form action="{{ route('services.order_package', [$service->slug, $package->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 text-white py-1.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                                            Chọn gói này
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="bg-gray-50 p-3 text-right border-t border-gray-100">
                                    <span class="bg-gray-200 text-gray-600 py-1.5 px-4 rounded-md text-sm inline-block">
                                        Không khả dụng
                                    </span>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Hiện không có gói dịch vụ nào khả dụng. Vui lòng quay lại sau.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @auth
                    <div class="mt-6 text-center text-sm text-gray-500">
                        <p>Bạn sẽ được yêu cầu cung cấp thông tin tài khoản game sau khi chọn gói dịch vụ.</p>
                    </div>
                    @else
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Đăng nhập</a> hoặc 
                        <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">Đăng ký</a> để đặt dịch vụ
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 