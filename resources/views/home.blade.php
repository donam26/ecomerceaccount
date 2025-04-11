@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero Banner -->
    <div class="relative">
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Tài khoản Playtogerther chất lượng cao</h1>
                        <p class="text-xl mb-8">Chúng tôi cung cấp các tài khoản game Playtogerther uy tín, giá tốt nhất thị trường, giao dịch an toàn, bảo mật.</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('accounts.index') }}" class="btn-primary px-6 py-3 text-lg">Xem tài khoản</a>
                            <a href="{{ route('about') }}" class="border-2 border-white text-white px-6 py-3 rounded-lg hover:bg-white hover:text-blue-600 transition duration-300 text-lg">Tìm hiểu thêm</a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="{{ asset('images/banner.jpeg') }}" alt="Playtogerther Mobile" class="w-full h-auto rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tài khoản nổi bật -->
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Tài khoản nổi bật</h2>
                <p class="text-gray-600">Những tài khoản đặc biệt với nhiều ưu đãi</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($featuredAccounts as $account)
                    <div class="card overflow-hidden">
                        <div class="relative">
                            @if($account->original_price && $account->original_price > $account->price)
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    -{{ $account->getDiscountPercentageAttribute() }}%
                                </div>
                            @endif
                            
                            <!-- Ảnh tài khoản -->
                            <div class="h-40 overflow-hidden">
                                @php
                                    $accountImage = 'https://via.placeholder.com/300x200';
                                    if ($account->images) {
                                        if (is_string($account->images)) {
                                            $images = json_decode($account->images, true);
                                            if (is_array($images) && !empty($images)) {
                                                $accountImage = asset('storage/' . $images[0]);
                                            }
                                        } elseif (is_array($account->images) && !empty($account->images)) {
                                            $accountImage = asset('storage/' . $account->images[0]);
                                        }
                                    }
                                @endphp
                                <img src="{{ $accountImage }}" alt="{{ $account->title }}" class="w-full h-40 object-cover">
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">{{ $account->game->name }}</span>
                                @if($account->is_verified)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Đã xác thực
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="font-bold text-gray-800">{{ $account->title }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ Str::limit($account->description, 50) }}</p>
                            
                            @if($account->attributes)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @php
                                        $attributes = [];
                                        if (is_string($account->attributes)) {
                                            $attributes = json_decode($account->attributes, true) ?? [];
                                        } else if (is_array($account->attributes)) {
                                            $attributes = $account->attributes;
                                        }
                                    @endphp
                                    
                                    @foreach($attributes as $key => $value)
                                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded">
                                            {{ is_array($key) ? json_encode($key) : $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-bold text-blue-600">{{ number_format($account->price, 0, ',', '.') }}đ</span>
                                    @if($account->original_price && $account->original_price > $account->price)
                                        <span class="text-sm text-gray-500 line-through ml-1">{{ number_format($account->original_price, 0, ',', '.') }}đ</span>
                                    @endif
                                </div>
                                <a href="{{ route('accounts.show', $account->id) }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('accounts.index') }}" class="btn-primary">Xem tất cả tài khoản</a>
            </div>
        </div>
    </div>

    <!-- Lý do chọn chúng tôi -->
    <div class="bg-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Tại sao chọn chúng tôi?</h2>
                <p class="text-gray-600">Chúng tôi cam kết mang đến cho bạn trải nghiệm mua tài khoản game tốt nhất</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Uy tín hàng đầu</h3>
                    <p class="text-gray-600">Chúng tôi cam kết cung cấp tài khoản chất lượng, đúng như mô tả, mang đến sự hài lòng cho khách hàng.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">An toàn & Bảo mật</h3>
                    <p class="text-gray-600">Giao dịch an toàn, bảo mật thông tin khách hàng tuyệt đối, thanh toán đa dạng qua nhiều hình thức.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hỗ trợ 24/7</h3>
                    <p class="text-gray-600">Đội ngũ hỗ trợ chuyên nghiệp, luôn sẵn sàng giải đáp mọi thắc mắc và hỗ trợ bạn khi cần.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tài khoản mới nhất -->
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">SPAM KHẮP ĐẢO KAIA</h2>
                <p class="text-gray-600">Cập nhật liên tục các tài khoản mới nhất với nhiều ưu đãi</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($recentAccounts as $account)
                    <div class="card overflow-hidden">
                        <div class="relative">
                            @if($account->original_price && $account->original_price > $account->price)
                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    -{{ $account->getDiscountPercentageAttribute() }}%
                                </div>
                            @endif
                            
                            <!-- Ảnh tài khoản -->
                            <div class="h-40 overflow-hidden">
                                @php
                                    $accountImage = 'https://via.placeholder.com/300x200';
                                    if ($account->images) {
                                        if (is_string($account->images)) {
                                            $images = json_decode($account->images, true);
                                            if (is_array($images) && !empty($images)) {
                                                $accountImage = asset('storage/' . $images[0]);
                                            }
                                        } elseif (is_array($account->images) && !empty($account->images)) {
                                            $accountImage = asset('storage/' . $account->images[0]);
                                        }
                                    }
                                @endphp
                                <img src="{{ $accountImage }}" alt="{{ $account->title }}" class="w-full h-40 object-cover">
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">{{ $account->game->name }}</span>
                                @if($account->is_verified)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Đã xác thực
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="font-bold text-gray-800">{{ $account->title }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ Str::limit($account->description, 50) }}</p>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-bold text-blue-600">{{ number_format($account->price, 0, ',', '.') }}đ</span>
                                    @if($account->original_price && $account->original_price > $account->price)
                                        <span class="text-sm text-gray-500 line-through ml-1">{{ number_format($account->original_price, 0, ',', '.') }}đ</span>
                                    @endif
                                </div>
                                <a href="{{ route('accounts.show', $account->id) }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('accounts.index') }}" class="btn-primary">Xem tất cả tài khoản</a>
            </div>
        </div>
    </div>
@endsection 