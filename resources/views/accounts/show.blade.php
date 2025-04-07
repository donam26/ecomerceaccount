@extends('layouts.app')

@section('title', $account->title)

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-6 text-sm">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Trang chủ</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('games.show', $account->game->id) }}" class="text-blue-600 hover:text-blue-800">{{ $account->game->name }}</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-600">{{ $account->title }}</span>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <!-- Hình ảnh -->
                <div class="col-span-5 md:col-span-2">
                    <div class="mb-4">
                        @php
                            $images = [];
                            if ($account->images) {
                                if (is_string($account->images)) {
                                    $images = json_decode($account->images, true) ?? [];
                                } else if (is_array($account->images)) {
                                    $images = $account->images;
                                }
                            }
                        @endphp
                        
                        @if(count($images) > 0)
                            <div class="relative h-80 overflow-hidden rounded-lg">
                                <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $account->title }}" class="w-full h-full object-cover" id="mainImage">
                            </div>

                            <div class="mt-4 flex space-x-2 overflow-x-auto">
                                @if(count($images) > 1)
                                    <div class="flex space-x-2">
                                        @foreach($images as $index => $image)
                                            <div 
                                                class="cursor-pointer h-20 w-20 overflow-hidden rounded-md thumbnail-image {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}"
                                                data-image="{{ asset('storage/' . $image) }}"
                                                onclick="changeMainImage(this)"
                                            >
                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $account->title }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="relative h-80 overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center">
                                <p class="text-gray-500">Không có hình ảnh</p>
                            </div>
                        @endif
                    </div>

                    <!-- JavaScript để thay đổi hình ảnh chính -->
                    <script>
                        function changeMainImage(element) {
                            // Cập nhật hình ảnh chính
                            document.getElementById('mainImage').src = element.dataset.image;
                            
                            // Cập nhật trạng thái active của thumbnails
                            document.querySelectorAll('.thumbnail-image').forEach(thumb => {
                                thumb.classList.remove('ring-2', 'ring-blue-500');
                            });
                            element.classList.add('ring-2', 'ring-blue-500');
                        }
                    </script>
                </div>
                
                <!-- Thông tin tài khoản -->
                <div>
                    <div class="mb-2">
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-md">{{ $account->game->name }}</span>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $account->title }}</h1>
                    
                    <div class="flex items-center mb-6">
                        <div class="flex items-center text-2xl font-bold text-blue-600 mr-3">
                            {{ number_format($account->price, 0, ',', '.') }} đ
                        </div>
                        
                        @if($account->original_price && $account->original_price > $account->price)
                            <div class="flex items-center">
                                <span class="text-gray-500 line-through mr-2">{{ number_format($account->original_price, 0, ',', '.') }} đ</span>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">-{{ $account->getDiscountPercentageAttribute() }}%</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Thông tin tài khoản:</h3>
                        @if($account->attributes)
                            <div class="mt-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Đặc điểm tài khoản:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $attributes = [];
                                        if (is_string($account->attributes)) {
                                            $attributes = json_decode($account->attributes, true) ?? [];
                                        } else if (is_array($account->attributes)) {
                                            $attributes = $account->attributes;
                                        }
                                    @endphp
                                    
                                    @foreach($attributes as $key => $value)
                                        <span class="bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full">
                                            {{ is_array($key) ? json_encode($key) : $key }}: {{ is_array($value) ? json_encode($value) : $value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="text-gray-700 mb-6">
                            {{ $account->description }}
                        </div>
                    </div>
                    
                    @auth
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="account_id" value="{{ $account->id }}">
                            <button type="submit" class="btn-primary w-full py-3 text-lg">Mua ngay</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary block text-center w-full py-3 text-lg">Đăng nhập để mua</a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Tài khoản liên quan -->
        @if($relatedAccounts->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Tài khoản liên quan</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($relatedAccounts as $relatedAccount)
                        <div class="card overflow-hidden">
                            <div class="relative">
                                @if($relatedAccount->original_price && $relatedAccount->original_price > $relatedAccount->price)
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                        -{{ $relatedAccount->getDiscountPercentageAttribute() }}%
                                    </div>
                                @endif
                                
                                @php
                                    $accountImage = 'https://via.placeholder.com/300x200';
                                    if ($relatedAccount->images) {
                                        if (is_string($relatedAccount->images)) {
                                            $images = json_decode($relatedAccount->images, true);
                                            if (is_array($images) && !empty($images)) {
                                                $accountImage = asset('storage/' . $images[0]);
                                            }
                                        } elseif (is_array($relatedAccount->images) && !empty($relatedAccount->images)) {
                                            $accountImage = asset('storage/' . $relatedAccount->images[0]);
                                        }
                                    }
                                @endphp
                                <img src="{{ $accountImage }}" alt="{{ $relatedAccount->title }}" class="w-full h-40 object-cover">
                            </div>
                            
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800">{{ $relatedAccount->title }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ Str::limit($relatedAccount->description, 50) }}</p>
                                
                                <div class="mt-3 flex items-center justify-between">
                                    <div>
                                        <span class="text-xl font-bold text-blue-600">{{ number_format($relatedAccount->price, 0, ',', '.') }}đ</span>
                                        @if($relatedAccount->original_price && $relatedAccount->original_price > $relatedAccount->price)
                                            <span class="text-sm text-gray-500 line-through ml-1">{{ number_format($relatedAccount->original_price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('accounts.show', $relatedAccount->id) }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 