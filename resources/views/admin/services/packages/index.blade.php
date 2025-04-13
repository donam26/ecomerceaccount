@extends('layouts.admin')

@section('title', 'Quản lý gói dịch vụ - ' . $service->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Gói dịch vụ: {{ $service->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Quản lý các gói dịch vụ thuê câu cá</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.services.packages.create', $service->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Thêm gói mới
                </a>
                <a href="{{ route('admin.services.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
        <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
            <p class="font-bold">Thành công!</p>
            <p>{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
            <p class="font-bold">Lỗi!</p>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        <!-- Thông tin dịch vụ -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Thông tin dịch vụ</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Tên dịch vụ</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $service->name }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($service->status == 'active')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Hoạt động
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Tạm ngừng
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Giá gốc</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($service->price) }}đ</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Giá khuyến mãi</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($service->sale_price)
                            {{ number_format($service->sale_price) }}đ
                            @else
                            <span class="text-gray-400">Không có</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Danh sách gói dịch vụ -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Danh sách gói dịch vụ</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình ảnh</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên gói</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá khuyến mãi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thứ tự</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($packages as $package)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $package->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($package->image)
                                <div class="h-16 w-16 rounded-md overflow-hidden border border-gray-200">
                                    <img src="{{ asset($package->image) }}" alt="{{ $package->name }}" class="h-full w-full object-cover">
                                </div>
                                @else
                                <div class="h-16 w-16 flex items-center justify-center bg-gray-100 rounded-md border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $package->name }}</div>
                                @if($package->description)
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($package->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($package->price) }}đ</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($package->sale_price)
                                <span class="text-red-600 font-medium">{{ number_format($package->sale_price) }}đ</span>
                                @else
                                <span class="text-gray-400">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($package->status == 'active')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Hoạt động
                                </span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Tạm ngừng
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $package->display_order ?: 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.services.packages.edit', ['service' => $service->id, 'package' => $package->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Sửa
                                </a>
                                <form action="{{ route('admin.services.packages.destroy', ['service' => $service->id, 'package' => $package->id]) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bạn có chắc chắn muốn xóa gói dịch vụ này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Không có gói dịch vụ nào. <a href="{{ route('admin.services.packages.create', $service->id) }}" class="text-blue-600 hover:text-blue-900">Thêm gói mới</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 