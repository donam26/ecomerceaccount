@extends('layouts.admin')

@section('title', 'Thêm danh mục tài khoản mới')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Thêm danh mục tài khoản mới</h1>
            <a href="{{ route('admin.account_categories.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.account_categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên danh mục <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700">Thứ tự hiển thị</label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('display_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Số nhỏ hơn sẽ hiển thị trước</p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea name="description" id="description" rows="4"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                            <input type="file" name="image" id="image"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Kích thước tối đa: 2MB. Định dạng: jpg, jpeg, png, gif</p>
                        </div>
                        
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">Kích hoạt</label>
                            </div>
                            <p class="text-xs text-gray-500">Danh mục sẽ được hiển thị trên trang web nếu được kích hoạt</p>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_featured" class="ml-2 block text-sm text-gray-700">Nổi bật</label>
                            </div>
                            <p class="text-xs text-gray-500">Danh mục sẽ được ưu tiên hiển thị trên trang chủ nếu được đánh dấu là nổi bật</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Lưu danh mục
                    </button>
                    <a href="{{ route('admin.account_categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 ml-2">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 