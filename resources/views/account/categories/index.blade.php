@extends('layouts.app')

@section('title', 'Danh mục tài khoản')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Danh mục tài khoản</h1>
            <p class="text-gray-600 mt-2">Chọn danh mục tài khoản bạn quan tâm</p>
        </div>
        
        <!-- Danh sách danh mục -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($categories as $category)
            <a href="{{ route('account.category', $category->slug) }}" class="block group">
                <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105">
                    <div class="h-48 overflow-hidden">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:opacity-90">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">Không có ảnh</span>
                            </div>
                        @endif
                        @if($category->is_featured)
                            <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded">
                                Nổi bật
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-900 group-hover:text-blue-600">{{ $category->name }}</h2>
                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($category->description, 100) }}</p>
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-blue-600 font-medium">{{ $category->availableAccounts()->count() }} tài khoản</span>
                            <span class="text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded group-hover:bg-blue-100">Xem chi tiết</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        @if($categories->isEmpty())
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.415 0 3 3 0 014.242 0 1 1 0 001.415-1.415 5 5 0 00-7.072 0 1 1 0 000 1.415z" clip-rule="evenodd" />
                </svg>
                <p class="text-xl text-gray-600">Không có danh mục tài khoản nào.</p>
            </div>
        @endif
    </div>
</div>
@endsection 