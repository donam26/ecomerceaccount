@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Thông tin tài khoản</h1>
    
    <!-- Form cập nhật thông tin -->
    <div class="bg-white rounded-lg shadow-md p-6">
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Thông tin cá nhân</h2>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Họ tên -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Số điện thoại -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Đổi mật khẩu</h2>
                <p class="text-sm text-gray-600 mb-4">Để trống nếu bạn không muốn thay đổi mật khẩu.</p>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Mật khẩu hiện tại -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" id="current_password" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Mật khẩu mới -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                        <input type="password" name="new_password" id="new_password" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('new_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Xác nhận mật khẩu mới -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <!-- Ví điện tử -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Thông tin ví điện tử</h2>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium">Số dư hiện tại:</span>
                        <span class="text-lg font-bold text-blue-600">{{ number_format($user->wallet ? $user->wallet->balance : 0, 0, ',', '.') }}đ</span>
                    </div>
                    
                    <a href="{{ route('wallet.index') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Quản lý ví điện tử
                    </a>
                </div>
            </div>
            
            <!-- Lịch sử đơn hàng -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Đơn hàng gần đây</h2>
                
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <a href="{{ route('orders.index') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Đơn hàng tài khoản</h3>
                                <p class="text-sm text-gray-600">Xem lịch sử mua tài khoản game</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('boosting.my_orders') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Đơn hàng dịch vụ</h3>
                                <p class="text-sm text-gray-600">Xem lịch sử dịch vụ cày thuê</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cập nhật thông tin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 