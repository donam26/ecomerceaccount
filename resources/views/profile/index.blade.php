@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('breadcrumbs')
<div class="flex items-center text-sm">
    <a href="{{ route('home') }}" class="text-gray-500 hover:text-indigo-600">Trang chủ</a>
    <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
    <span class="text-indigo-600 font-medium">Thông tin tài khoản</span>
</div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-500 rounded-xl shadow-lg p-6 mb-8 text-white overflow-hidden relative">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-5">
                <div class="flex-shrink-0 w-20 h-20 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="bi bi-person-circle text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                    <div class="flex items-center mt-1 text-gray-200">
                        <i class="bi bi-envelope-fill mr-1"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                    <div class="flex items-center mt-1 text-gray-200">
                        <i class="bi bi-telephone-fill mr-1"></i>
                        <span>{{ $user->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="md:text-right mt-4 md:mt-0">
                <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm inline-block mb-2">
                    <div class="text-sm">Số dư ví</div>
                    <div class="text-2xl font-bold">{{ number_format($user->wallet ? $user->wallet->balance : 0, 0, ',', '.') }}đ</div>
                </div>
                <div class="flex gap-2 flex-wrap justify-start md:justify-end">
                    <a href="{{ route('wallet.deposit') }}" class="bg-white text-indigo-600 hover:bg-gray-100 rounded-lg px-4 py-2 font-medium inline-flex items-center shadow-sm transition-all hover:shadow">
                        <i class="bi bi-plus-circle mr-2"></i>
                        Nạp tiền
                    </a>
                    <a href="{{ route('wallet.index') }}" class="bg-indigo-700 hover:bg-indigo-800 text-white rounded-lg px-4 py-2 font-medium inline-flex items-center shadow-sm transition-all hover:shadow">
                        <i class="bi bi-wallet2 mr-2"></i>
                        Quản lý ví
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1" data-aos="fade-right">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Truy cập nhanh</h3>
                </div>
                <nav class="p-2">
                    <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="bi bi-bag text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-800">Đơn hàng tài khoản</span>
                            <span class="text-sm text-gray-500">Xem lịch sử mua tài khoản</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('boosting.my_orders') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="bi bi-graph-up-arrow text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-800">Đơn hàng dịch vụ</span>
                            <span class="text-sm text-gray-500">Xem lịch sử dịch vụ cày thuê</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('accounts.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="bi bi-controller text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-800">Mua tài khoản</span>
                            <span class="text-sm text-gray-500">Xem danh sách tài khoản game</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('services.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="bi bi-gear text-indigo-600"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block font-medium text-gray-800">Dịch vụ</span>
                            <span class="text-sm text-gray-500">Khám phá các dịch vụ cày thuê</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>
                </nav>
            </div>
        </div>
    
        <!-- Main Content -->
        <div class="lg:col-span-2" data-aos="fade-left">
            <!-- Form cập nhật thông tin -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="border-b border-gray-100">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#profile-info" class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600 active">
                                <i class="bi bi-person mr-2"></i>Thông tin cá nhân
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#password" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                                <i class="bi bi-lock mr-2"></i>Đổi mật khẩu
                            </a>
                        </li>
                    </ul>
                </div>
                
                @if (session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 m-4 rounded" role="alert">
                        <div class="flex">
                            <i class="bi bi-check-circle text-green-500 mr-3"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('profile.update') }}" method="POST" class="p-5">
                    @csrf
                    @method('PUT')
                    
                    <div id="profile-info" class="mb-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Họ tên -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-person text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Số điện thoại -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-telephone text-gray-400"></i>
                                    </div>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div id="password" class="mb-6 hidden">
                        <div class="bg-indigo-50 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <i class="bi bi-info-circle text-indigo-600 mt-1 mr-3"></i>
                                <p class="text-sm text-indigo-700">Để trống các trường mật khẩu nếu bạn không muốn thay đổi mật khẩu.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Mật khẩu hiện tại -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="current_password" id="current_password" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Mật khẩu mới -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-shield-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="new_password" id="new_password" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                @error('new_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Xác nhận mật khẩu mới -->
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-shield-check text-gray-400"></i>
                                    </div>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                        class="w-full pl-10 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2.5 inline-flex items-center">
                            <i class="bi bi-check-lg mr-2"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[href^="#"]');
        const tabContents = document.querySelectorAll('[id^="profile-"], [id^="password"]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('border-indigo-600', 'text-indigo-600');
                    t.classList.add('border-transparent');
                });
                
                // Add active class to current tab
                this.classList.add('border-indigo-600', 'text-indigo-600');
                this.classList.remove('border-transparent');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show current tab content
                const target = this.getAttribute('href').substring(1);
                document.getElementById(target).classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection 