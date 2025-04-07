@extends('layouts.admin')

@section('title', 'Chỉnh sửa trò chơi')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chỉnh sửa trò chơi: {{ $game->name }}</h1>
            <a href="{{ route('admin.games.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.games.update', $game->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Tên trò chơi -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên trò chơi</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $game->name) }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $game->slug) }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Slug sẽ được sử dụng trong URL. Ví dụ: "lien-quan-mobile"</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description', $game->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hình ảnh hiện tại -->
                        @if($game->image)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hình ảnh hiện tại</label>
                                <div class="mt-1">
                                    <img src="{{ Storage::url($game->image) }}" alt="{{ $game->name }}" class="w-32 h-32 object-cover rounded-md">
                                </div>
                            </div>
                        @endif

                        <!-- Hình ảnh mới -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Hình ảnh mới</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="image" id="image"
                                       class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Hình ảnh đại diện cho trò chơi (khuyến nghị: 512x512px)</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Banner hiện tại -->
                        @if($game->banner)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Banner hiện tại</label>
                                <div class="mt-1">
                                    <img src="{{ Storage::url($game->banner) }}" alt="{{ $game->name }} banner" class="w-full h-32 object-cover rounded-md">
                                </div>
                            </div>
                        @endif

                        <!-- Banner mới -->
                        <div>
                            <label for="banner" class="block text-sm font-medium text-gray-700">Banner mới</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="banner" id="banner"
                                       class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Hình ảnh banner cho trò chơi (khuyến nghị: 1920x400px)</p>
                            @error('banner')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Cập nhật trò chơi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 