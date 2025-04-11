@extends('layouts.admin')

@section('title', 'Chỉnh sửa gói dịch vụ - ' . $package->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Chỉnh sửa gói dịch vụ</h1>
                <p class="mt-1 text-sm text-gray-600">Dịch vụ: {{ $service->name }} - Gói: {{ $package->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.services.packages', $service->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Thông báo lỗi -->
        @if ($errors->any())
        <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p class="font-bold">Đã xảy ra lỗi!</p>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form chỉnh sửa gói dịch vụ -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('admin.services.packages.update', ['service' => $service->id, 'package' => $package->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Tên gói dịch vụ -->
                        <div class="sm:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên gói dịch vụ <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                        </div>

                        <!-- Giá gốc -->
                        <div class="sm:col-span-3">
                            <label for="price" class="block text-sm font-medium text-gray-700">Giá gói (VNĐ) <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <input type="number" name="price" id="price" min="0" value="{{ old('price', $package->price) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                            </div>
                        </div>

                        <!-- Giá khuyến mãi -->
                        <div class="sm:col-span-3">
                            <label for="sale_price" class="block text-sm font-medium text-gray-700">Giá khuyến mãi (VNĐ)</label>
                            <div class="mt-1">
                                <input type="number" name="sale_price" id="sale_price" min="0" value="{{ old('sale_price', $package->sale_price) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Để trống nếu không có khuyến mãi</p>
                        </div>

                        <!-- Thứ tự hiển thị -->
                        <div class="sm:col-span-3">
                            <label for="display_order" class="block text-sm font-medium text-gray-700">Thứ tự hiển thị</label>
                            <div class="mt-1">
                                <input type="number" name="display_order" id="display_order" min="0" value="{{ old('display_order', $package->display_order) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Số nhỏ hơn sẽ hiển thị trước</p>
                        </div>

                        <!-- Trạng thái -->
                        <div class="sm:col-span-3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <select name="status" id="status" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="active" {{ old('status', $package->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="inactive" {{ old('status', $package->status) == 'inactive' ? 'selected' : '' }}>Tạm ngừng</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả gói dịch vụ</label>
                            <div class="mt-1">
                                <textarea name="description" id="description" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $package->description) }}</textarea>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Mô tả ngắn gọn về gói dịch vụ này</p>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Đã tạo: {{ $package->created_at->format('d/m/Y H:i') }}</span>
                        @if($package->updated_at)
                        <span class="ml-4 text-sm text-gray-500">Cập nhật lần cuối: {{ $package->updated_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cập nhật gói dịch vụ
                    </button>
                </div>
            </form>
        </div>

        <!-- Thống kê đơn hàng -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Thông tin đơn hàng</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Thống kê đơn hàng của gói dịch vụ này</p>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tổng số đơn hàng</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $package->orders->count() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Đơn hàng thành công</dt>
                        <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $package->orders->where('status', 'completed')->count() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Đơn hàng đang xử lý</dt>
                        <dd class="mt-1 text-2xl font-semibold text-blue-600">{{ $package->orders->whereIn('status', ['pending', 'processing'])->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection 