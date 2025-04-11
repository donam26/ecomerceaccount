@extends('layouts.admin')

@section('title', 'Chỉnh sửa dịch vụ')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chỉnh sửa dịch vụ: {{ $service->name }}</h1>
            <a href="{{ route('admin.services.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại danh sách
            </a>
        </div>
        
        <!-- Thông báo hướng dẫn -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Giá dịch vụ được thiết lập trong các gói dịch vụ con. Bạn có thể quản lý các gói dịch vụ sau khi lưu.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form dịch vụ -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="game_id" class="block text-sm font-medium text-gray-700">
                                Game <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select id="game_id" name="game_id" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('game_id') border-red-500 @enderror" required>
                                    <option value="">-- Chọn game --</option>
                                    @foreach ($games as $game)
                                        <option value="{{ $game->id }}" {{ (old('game_id', $service->game_id) == $game->id) ? 'selected' : '' }}>
                                            {{ $game->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('game_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Tên dịch vụ <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror" required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Loại dịch vụ <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select id="type" name="type" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('type') border-red-500 @enderror" required>
                                    <option value="fishing" {{ old('type', $service->type) == 'fishing' ? 'selected' : '' }}>Câu cá</option>
                                    <option value="account" {{ old('type', $service->type) == 'account' ? 'selected' : '' }}>Tài khoản</option>
                                    <option value="boosting" {{ old('type', $service->type) == 'boosting' ? 'selected' : '' }}>Cày thuê</option>
                                    <option value="other" {{ old('type', $service->type) == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Trạng thái <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select id="status" name="status" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('status') border-red-500 @enderror" required>
                                    <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Tạm ngừng</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="image" class="block text-sm font-medium text-gray-700">
                                Hình ảnh
                            </label>
                            <div class="mt-1">
                                <input type="file" id="image" name="image" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 @error('image') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">Hình ảnh nên có kích thước 800x600px và không quá 2MB</p>
                                @if($service->image)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700">Hình ảnh hiện tại:</p>
                                    <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="mt-2 max-h-40 rounded shadow">
                                </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Mô tả dịch vụ
                            </label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="5" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-500 @enderror">{{ old('description', $service->description) }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_featured" name="is_featured" type="checkbox" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }} class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_featured" class="font-medium text-gray-700">Hiển thị nổi bật</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.services.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Hủy
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cập nhật dịch vụ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Thêm TinyMCE cho textarea mô tả
        if(typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#description',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            });
        }
    });
</script>
@endsection 