@extends('layouts.app')

@section('title', 'Danh sách tài khoản')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Danh sách tài khoản</h1>
            
            <!-- Bộ lọc -->
            <div class="bg-white p-4 rounded-lg shadow mt-6">
                <form action="{{ route('accounts.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                        <select id="sort" name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp đến cao</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao đến thấp</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 w-full">Lọc kết quả</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Danh sách tài khoản -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($accounts as $account)
                <div class="card">
                    <div class="relative">
                        @if($account->original_price && $account->original_price > $account->price)
                            <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                -{{ $account->getDiscountPercentageAttribute() }}%
                            </div>
                        @endif
                        
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                            $attributes = $account->attributes;
                                            if (is_string($attributes)) {
                                                $attributes = json_decode($attributes, true) ?? [];
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
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($accounts->isEmpty())
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.415 0 3 3 0 014.242 0 1 1 0 001.415-1.415 5 5 0 00-7.072 0 1 1 0 000 1.415z" clip-rule="evenodd" />
                </svg>
                <p class="text-xl text-gray-600">Không tìm thấy tài khoản nào.</p>
            </div>
        @else
            <!-- Phân trang -->
            <div class="mt-8">
                {{ $accounts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 