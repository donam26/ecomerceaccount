@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản game')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chi tiết tài khoản: {{ $account->title }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('admin.accounts.edit', $account->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.accounts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Quay lại
                </a>
            </div>
        </div>
        
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin tài khoản</h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->id }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Trò chơi</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->game->name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tiêu đề</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->title }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Mô tả</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->description ?? 'Không có mô tả' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tên đăng nhập</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->username }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Mật khẩu</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->password }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Giá bán</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($account->price, 0, ',', '.') }} đ</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Giá gốc</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($account->original_price)
                                {{ number_format($account->original_price, 0, ',', '.') }} đ
                                <span class="text-red-600 ml-2">(-{{ $account->getDiscountPercentageAttribute() }}%)</span>
                            @else
                                Không có
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            @if($account->status == 'available')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Có sẵn
                                </span>
                            @elseif($account->status == 'sold')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Đã bán
                                </span>
                            @elseif($account->status == 'pending')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Đang xử lý
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $account->status }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    
                    <!-- Thuộc tính -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Thuộc tính</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @php
                                $attributes = [];
                                if ($account->attributes) {
                                    if (is_string($account->attributes)) {
                                        $attributes = json_decode($account->attributes, true) ?? [];
                                    } else if (is_array($account->attributes)) {
                                        $attributes = $account->attributes;
                                    }
                                }
                            @endphp
                            
                            @if(count($attributes) > 0)
                                <div class="space-y-2">
                                    @foreach($attributes as $key => $value)
                                        @if($key && $value)
                                            <div class="bg-gray-100 px-3 py-2 rounded-md">
                                                <span class="font-medium">{{ $key }}:</span> {{ $value }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p>Không có thuộc tính</p>
                            @endif
                        </dd>
                    </div>
                    
                    <!-- Hình ảnh -->
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Hình ảnh</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @php
                                $images = [];
                                if ($account->images) {
                                    if (is_string($account->images)) {
                                        $images = json_decode($account->images, true) ?? [];
                                    } else if (is_array($account->images)) {
                                        $images = $account->images;
                                    }
                                }
                            @endphp
                            
                            @if(count($images) > 0)
                                <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach($images as $image)
                                        <div class="overflow-hidden rounded-lg">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Ảnh tài khoản" class="h-24 w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p>Không có hình ảnh</p>
                            @endif
                        </dd>
                    </div>
                    
                    <!-- Thời gian -->
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Tạo lúc</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->created_at->format('d/m/Y H:i:s') }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Cập nhật lúc</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $account->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <!-- Nút xóa -->
        <div class="mt-6 flex justify-end">
            <form action="{{ route('admin.accounts.destroy', $account->id) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" 
                        onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                    Xóa tài khoản
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 