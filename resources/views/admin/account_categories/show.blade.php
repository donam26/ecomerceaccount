@extends('layouts.admin')

@section('title', 'Chi tiết danh mục tài khoản')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chi tiết danh mục: {{ $accountCategory->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.account_categories.edit', $accountCategory->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.account_categories.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Quay lại
                </a>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Thông tin cơ bản -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin danh mục</h3>
                </div>
                
                <div class="border-t border-gray-200">
                    <div class="flex justify-center p-6">
                        @if($accountCategory->image)
                        <img src="{{ asset('storage/' . $accountCategory->image) }}" alt="{{ $accountCategory->name }}" class="h-48 w-auto object-cover rounded-md">
                        @else
                        <div class="bg-gray-200 h-48 w-full flex items-center justify-center rounded-md">
                            <span class="text-gray-500 text-sm">Không có hình ảnh</span>
                        </div>
                        @endif
                    </div>
                    
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->id }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tên danh mục</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->name }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->slug }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->description ?? 'Không có mô tả' }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Thứ tự hiển thị</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->display_order }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2">
                                @if($accountCategory->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Hoạt động
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    Không hoạt động
                                </span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nổi bật</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2">
                                @if($accountCategory->is_featured)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    Nổi bật
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    Thường
                                </span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ngày cập nhật</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $accountCategory->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                    </dl>
                    
                    <div class="px-4 py-3 bg-gray-50 sm:px-6">
                        <a href="{{ route('account.category', $accountCategory->slug) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Xem trên trang chủ
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Danh sách tài khoản trong danh mục -->
            <div class="md:col-span-2 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Tài khoản trong danh mục ({{ $accountCategory->accounts->count() }})
                    </h3>
                </div>
                
                <div class="border-t border-gray-200">
                    @if($accountCategory->accounts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Game</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($accountCategory->accounts->take(10) as $account)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $account->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->game->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($account->price) }}đ</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($account->status == 'available')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Có sẵn
                                        </span>
                                        @elseif($account->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            Đang giữ
                                        </span>
                                        @elseif($account->status == 'sold')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Đã bán
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.accounts.edit', $account->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Sửa
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($accountCategory->accounts->count() > 10)
                    <div class="px-6 py-4 text-center">
                        <p class="text-sm text-gray-500">Hiển thị 10/{{ $accountCategory->accounts->count() }} tài khoản</p>
                    </div>
                    @endif
                    @else
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm text-gray-500">Chưa có tài khoản nào trong danh mục này</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 