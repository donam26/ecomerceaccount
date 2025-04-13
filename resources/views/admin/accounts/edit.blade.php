@extends('layouts.admin')

@section('title', 'Chỉnh sửa tài khoản game')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chỉnh sửa tài khoản game</h1>
            <a href="{{ route('admin.accounts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.accounts.update', $account->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Trò chơi -->
                        <div>
                            <label for="game_id" class="block text-sm font-medium text-gray-700">Trò chơi</label>
                            <select id="game_id" name="game_id" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Chọn trò chơi</option>
                                @foreach($games as $game)
                                    <option value="{{ $game->id }}" {{ (old('game_id') ?? $account->game_id) == $game->id ? 'selected' : '' }}>
                                        {{ $game->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('game_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Danh mục tài khoản -->
                        <div>
                            <label for="account_category_id" class="block text-sm font-medium text-gray-700">Danh mục tài khoản</label>
                            <select id="account_category_id" name="account_category_id"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('account_category_id') ?? $account->account_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Có thể để trống</p>
                            @error('account_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tiêu đề -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                            <input type="text" name="title" id="title" value="{{ old('title') ?? $account->title }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                            <input type="text" name="username" id="username" value="{{ old('username') ?? $account->username }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                            <input type="text" name="password" id="password" value="{{ old('password') ?? $account->password }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Giá -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Giá (đ)</label>
                            <input type="number" name="price" id="price" value="{{ old('price') ?? $account->price }}" required min="0"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Giá gốc -->
                        <div>
                            <label for="original_price" class="block text-sm font-medium text-gray-700">Giá gốc (đ)</label>
                            <input type="number" name="original_price" id="original_price" value="{{ old('original_price') ?? $account->original_price }}" min="0"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Để trống nếu không có khuyến mãi</p>
                            @error('original_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') ?? $account->description }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hình ảnh hiện tại -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hình ảnh hiện tại</label>
                            <div class="mt-2 grid grid-cols-3 md:grid-cols-5 gap-3">
                                @if(is_array($account->images) && count($account->images) > 0)
                                    @foreach($account->images as $image)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Hình ảnh tài khoản" class="h-20 w-full object-cover rounded-md">
                                            <div class="mt-1">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="remove_images[]" value="{{ $image }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-600">Xóa</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500">Không có hình ảnh</p>
                                @endif
                            </div>
                        </div>

                        <!-- Thêm hình ảnh mới -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Thêm hình ảnh mới</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="new_images[]" id="new_images" multiple
                                       class="border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Có thể chọn nhiều hình ảnh. Để trống nếu không muốn thêm hình ảnh mới.</p>
                            @error('new_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('new_images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trạng thái -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select id="status" name="status" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="available" {{ (old('status') ?? $account->status) == 'available' ? 'selected' : '' }}>Có sẵn</option>
                                <option value="sold" {{ (old('status') ?? $account->status) == 'sold' ? 'selected' : '' }}>Đã bán</option>
                                <option value="pending" {{ (old('status') ?? $account->status) == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Cập nhật tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 