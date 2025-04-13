@extends('layouts.app')

@section('title', $category->name . ' - Danh mục tài khoản')

@section('content')
<div class="py-6 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                {{ $category->description ?? 'Danh sách tài khoản thuộc danh mục '.$category->name.'. Tìm và lựa chọn tài khoản phù hợp với nhu cầu của bạn.' }}
            </p>
        </div>

        <!-- Bộ lọc -->
        <div class="mb-8">
            <form action="{{ route('account.category.filter', $category->slug) }}" method="GET" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                        <select id="sort_by" name="sort_by" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_low" {{ request('sort_by') == 'price_low' ? 'selected' : '' }}>Giá thấp đến cao</option>
                            <option value="price_high" {{ request('sort_by') == 'price_high' ? 'selected' : '' }}>Giá cao đến thấp</option>
                        </select>
                    </div>

                    @if(count($games) > 0)
                    <div>
                        <label for="game_id" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <select id="game_id" name="game_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tất cả trò chơi</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Lọc kết quả
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danh sách tài khoản -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
            @forelse($accounts as $account)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
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
                    <img src="{{ $accountImage }}" alt="{{ $account->title }}" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-blue-600 text-white text-sm font-bold px-3 py-1 m-2 rounded-md">
                        {{ $account->game->name }}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 truncate">{{ $account->title }}</h3>
                    
                    <div class="flex items-center text-gray-600 text-sm mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $account->created_at->diffForHumans() }}</span>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($account->description, 120) }}</p>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-xl font-bold text-blue-600">{{ number_format($account->price, 0, ',', '.') }}đ</span>
                            @if($account->original_price && $account->original_price > $account->price)
                                <span class="text-sm text-gray-500 line-through ml-1">{{ number_format($account->original_price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <a href="{{ route('accounts.show', $account->id) }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-4 bg-white rounded-lg shadow-md p-6 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Không tìm thấy tài khoản</h3>
                <p class="text-gray-600 mb-4">Chúng tôi không tìm thấy tài khoản nào trong danh mục này.</p>
                <a href="{{ route('accounts.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Xem tất cả tài khoản
                </a>
            </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-6">
            {{ $accounts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection 