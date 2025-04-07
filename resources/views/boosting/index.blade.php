@extends('layouts.app')

@section('title', 'Dịch vụ cày thuê')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Dịch vụ cày thuê</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Để lại tài khoản cho chúng tôi và thư giãn. Đội ngũ chuyên nghiệp sẽ giúp bạn đạt được mục tiêu trong game một cách nhanh chóng và an toàn.
            </p>
        </div>

        <!-- Bộ lọc -->
        <div class="mb-8">
            <form action="{{ route('boosting.index') }}" method="GET" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="game" class="block text-sm font-medium text-gray-700 mb-1">Game</label>
                        <select id="game" name="game" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tất cả game</option>
                            @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ request('game') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                        <select id="sort" name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>Tên (A-Z)</option>
                            <option value="name-desc" {{ request('sort') == 'name-desc' ? 'selected' : '' }}>Tên (Z-A)</option>
                            <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Giá (thấp đến cao)</option>
                            <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Giá (cao đến thấp)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nhập tên dịch vụ..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Lọc kết quả
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Danh sách dịch vụ -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @forelse($services as $service)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="{{ $service->thumbnail ? asset('storage/' . $service->thumbnail) : asset('images/default-service.jpg') }}" 
                        alt="{{ $service->name }}" class="w-full h-48 object-cover">
                    <div class="absolute top-0 right-0 bg-blue-600 text-white text-sm font-bold px-3 py-1 m-2 rounded-md">
                        {{ $service->game->name }}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 truncate">{{ $service->name }}</h3>
                    
                    <div class="flex items-center text-gray-600 text-sm mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Thời gian ước tính: {{ $service->estimated_days }} ngày</span>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            @if($service->sale_price && $service->sale_price < $service->price)
                            <p class="text-gray-500 line-through text-sm">{{ number_format($service->price, 0, ',', '.') }}đ</p>
                            <p class="text-xl font-bold text-red-600">{{ number_format($service->sale_price, 0, ',', '.') }}đ</p>
                            @else
                            <p class="text-xl font-bold text-gray-900">{{ number_format($service->price, 0, ',', '.') }}đ</p>
                            @endif
                        </div>
                        <a href="{{ route('boosting.show', $service->slug) }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-white rounded-lg shadow-md p-6 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Không tìm thấy dịch vụ</h3>
                <p class="text-gray-600 mb-4">Chúng tôi không tìm thấy dịch vụ nào phù hợp với tiêu chí tìm kiếm của bạn.</p>
                <a href="{{ route('boosting.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Xem tất cả dịch vụ
                </a>
            </div>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-6">
            {{ $services->withQueryString()->links() }}
        </div>
    </div>
</div>

@endsection 