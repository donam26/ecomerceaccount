@extends('layouts.app')

@section('title', 'Danh sách tài khoản')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center text-white">
            <h1 class="text-4xl font-bold mb-4">Tài khoản game chất lượng cao</h1>
            <p class="text-xl opacity-90 mb-8">Lựa chọn an toàn, uy tín dành cho game thủ</p>
            <div class="bg-white/20 backdrop-blur-sm p-6 rounded-xl shadow-lg">
                <form action="{{ route('accounts.search') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <input type="text" name="keyword" placeholder="Tìm kiếm tài khoản..." class="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-6 py-3 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-medium transition duration-200">
                        Tìm kiếm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Danh mục tài khoản -->
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Danh mục tài khoản</h2>
                <a href="{{ route('account.categories') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                    Xem tất cả
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $category)
                <a href="{{ route('account.category', $category->slug) }}" class="block group">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:translate-y-[-5px]">
                        <div class="h-36 overflow-hidden relative">
                            @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $category->name }}</h3>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-sm text-gray-600">{{ $category->accounts()->where('status', 'available')->count() }} tài khoản</span>
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Bộ lọc -->
        <div class="bg-white p-6 rounded-xl shadow-md mb-8">
            <form action="{{ route('accounts.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="game_id" class="block text-sm font-medium text-gray-700 mb-2">Trò chơi</label>
                    <select id="game_id" name="game_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tất cả trò chơi</option>
                        @foreach(\App\Models\Game::orderBy('name')->get() as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                    <select id="category_id" name="category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                    <select id="price_range" name="price_range" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tất cả mức giá</option>
                        <option value="0-100000" {{ request('price_range') == '0-100000' ? 'selected' : '' }}>Dưới 100.000đ</option>
                        <option value="100000-500000" {{ request('price_range') == '100000-500000' ? 'selected' : '' }}>100.000đ - 500.000đ</option>
                        <option value="500000-1000000" {{ request('price_range') == '500000-1000000' ? 'selected' : '' }}>500.000đ - 1.000.000đ</option>
                        <option value="1000000-5000000" {{ request('price_range') == '1000000-5000000' ? 'selected' : '' }}>1.000.000đ - 5.000.000đ</option>
                        <option value="5000000-0" {{ request('price_range') == '5000000-0' ? 'selected' : '' }}>Trên 5.000.000đ</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                    <select id="sort" name="sort" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp đến cao</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao đến thấp</option>
                    </select>
                </div>
                
                <div class="md:col-span-4">
                    <button type="submit" class="w-full md:w-auto float-right bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200">
                        Lọc kết quả
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Danh sách tài khoản -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Tài khoản nổi bật</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
            @foreach($accounts as $account)
            <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:translate-y-[-5px]">
                <div class="relative">
                    @if($account->original_price && $account->original_price > $account->price)
                    <div class="absolute top-3 right-3 z-10 bg-red-500 text-white text-sm font-bold px-2.5 py-1.5 rounded-lg">
                        -{{ $account->getDiscountPercentageAttribute() }}%
                    </div>
                    @endif
                    
                    @if($account->is_featured)
                    <div class="absolute top-3 left-3 z-10 bg-amber-500 text-white text-sm font-bold px-2.5 py-1.5 rounded-lg">
                        Nổi bật
                    </div>
                    @endif
                    
                    <div class="h-48 overflow-hidden">
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
                        <img src="{{ $accountImage }}" alt="{{ $account->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                </div>
                
                <div class="p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">{{ $account->game->name }}</span>
                        @if($account->is_verified)
                        <span class="flex items-center text-green-600 text-xs font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Đã xác thực
                        </span>
                        @endif
                    </div>
                    
                    <h3 class="font-bold text-gray-900 mb-2 text-lg line-clamp-2">{{ $account->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $account->description }}</p>
                    
                    @if($account->attributes && is_array($account->attributes) && count($account->attributes) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(array_slice($account->attributes, 0, 3) as $key => $value)
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                            {{ $key }}: {{ $value }}
                        </span>
                        @endforeach
                        
                        @if(count($account->attributes) > 3)
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                            +{{ count($account->attributes) - 3 }}
                        </span>
                        @endif
                    </div>
                    @endif
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div>
                            <span class="text-xl font-bold text-blue-600">{{ number_format($account->price, 0, ',', '.') }}đ</span>
                            @if($account->original_price && $account->original_price > $account->price)
                            <span class="text-sm text-gray-500 line-through ml-1">{{ number_format($account->original_price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <a href="{{ route('accounts.show', $account->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($accounts->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.415 0 3 3 0 014.242 0 1 1 0 001.415-1.415 5 5 0 00-7.072 0 1 1 0 000 1.415z" clip-rule="evenodd" />
            </svg>
            <p class="text-2xl font-medium text-gray-700">Không tìm thấy tài khoản nào.</p>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Hiện không có tài khoản nào phù hợp với tiêu chí tìm kiếm của bạn. Vui lòng thử lại với các bộ lọc khác.</p>
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