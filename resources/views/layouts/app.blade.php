<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ShopBuffsao') }} - @yield('title', 'Trang chủ')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Heroicons -->
    <script src="https://unpkg.com/heroicons@1.0.6/dist/solid.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="logo-text">
                                ShopBuffsao
                            </a>
                        </div>
                        
                        <!-- Navigation Links -->
                        <div class="hidden sm:ml-6 sm:flex space-x-4">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-white font-semibold' : 'border-transparent hover:border-gray-200' }}">
                                Trang chủ
                            </a>
                            <a href="{{ route('accounts.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('accounts.*') ? 'border-white font-semibold' : 'border-transparent hover:border-gray-200' }}">
                                Tài khoản
                            </a>
                            <a href="{{ route('boosting.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('boosting.*') ? 'border-white font-semibold' : 'border-transparent hover:border-gray-200' }}">
                                Cày thuê
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('contact') ? 'border-white font-semibold' : 'border-transparent hover:border-gray-200' }}">
                                Liên hệ
                            </a>
                        </div>
                    </div>
                    
                    <!-- Search -->
                    <div class="flex items-center">
                        <form action="{{ route('accounts.search') }}" method="GET" class="hidden md:block">
                            <div class="relative">
                                <input type="text" name="keyword" placeholder="Tìm kiếm tài khoản..." class="w-64 rounded-full py-1 px-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Authentication -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @guest
                            <a href="{{ route('login') }}" class="text-sm text-white hover:text-gray-200 mr-4">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="text-sm bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-md font-semibold">Đăng ký</a>
                        @else
                            <!-- Hiển thị số dư ví -->
                            <div class="flex items-center border-r border-blue-400 pr-3 mr-3">
                                <a href="{{ route('wallet.index') }}" class="flex items-center hover:text-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm11 1H6v8h8V6z" clip-rule="evenodd" />
                                        <path d="M14 8a1 1 0 100-2h-4a1 1 0 100 2h4z" />
                                    </svg>
                                    <span>Ví: {{ Auth::user()->wallet ? number_format(Auth::user()->wallet->balance, 0, ',', '.') : 0 }}đ</span>
                                </a>
                            </div>
                        
                            <div class="ml-3 relative">
                                <div class="flex items-center">
                                    <span class="mr-2">{{ Auth::user()->name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                
                                <!-- Dropdown menu -->
                                <div class="hidden absolute right-0 mt-2 w-48 py-2 bg-white rounded-md shadow-lg z-10">
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Quản trị viên
                                        </a>
                                    @endif
                                    
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Thông tin tài khoản
                                    </a>

                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Đơn hàng tài khoản
                                    </a>
                                    
                                    <a href="{{ route('boosting.my_orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Đơn hàng cày thuê
                                    </a>
                                    
                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="hidden mobile-menu sm:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('home') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Trang chủ
                    </a>
                    <a href="{{ route('games.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('games.*') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Trò chơi
                    </a>
                    <a href="{{ route('accounts.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('accounts.*') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Tài khoản
                    </a>
                    <a href="{{ route('boosting.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('boosting.*') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Cày thuê
                    </a>
                    <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('about') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Về chúng tôi
                    </a>
                    <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('contact') ? 'bg-blue-700 font-semibold' : 'hover:bg-blue-700' }}">
                        Liên hệ
                    </a>
                    
                    @guest
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">
                            Đăng ký
                        </a>
                    @else
                        <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">
                            Đơn hàng tài khoản
                        </a>
                        <a href="{{ route('boosting.my_orders') }}" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">
                            Đơn hàng cày thuê
                        </a>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-white hover:bg-blue-700">
                                Quản trị viên
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-white hover:bg-blue-700">
                                Đăng xuất
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </header>
        
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        @if (session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <p>{{ session('warning') }}</p>
            </div>
        @endif
        
        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Về chúng tôi</h4>
                        <p class="text-gray-300">
                            Playtogerther Shop là nơi cung cấp tài khoản game Playtogerther chất lượng, uy tín, với nhiều ưu đãi hấp dẫn.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Liên kết nhanh</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Trang chủ</a></li>
                            <li><a href="{{ route('games.index') }}" class="text-gray-300 hover:text-white">Trò chơi</a></li>
                            <li><a href="{{ route('accounts.index') }}" class="text-gray-300 hover:text-white">Tài khoản</a></li>
                            <li><a href="{{ route('boosting.index') }}" class="text-gray-300 hover:text-white">Cày thuê</a></li>
                            <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white">Về chúng tôi</a></li>
                            <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-white">Liên hệ</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Liên hệ</h4>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                123 Đường ABC, Quận XYZ, TP.HCM
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                </svg>
                                0123 456 789
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                contact@lienquanshop.com
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Playtogerther Shop. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // User dropdown toggle
            const userDropdownButton = document.querySelector('.ml-3.relative');
            const userDropdownMenu = document.querySelector('.ml-3.relative .hidden');
            
            if (userDropdownButton && userDropdownMenu) {
                userDropdownButton.addEventListener('click', function() {
                    userDropdownMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userDropdownButton.contains(event.target)) {
                        userDropdownMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    <!-- Biểu tượng mạng xã hội nổi -->
    <div class="social-float-buttons">
        <a href="https://www.facebook.com/people/Shopbuffsao/61574594802771/" target="_blank" class="social-float-button facebook text-white">
            <i class="bi bi-facebook text-2xl"></i>
        </a>
        <a href="https://zalo.me/0123456789" target="_blank" class="social-float-button zalo text-white">
            <span class="font-bold text-lg">Zalo</span>
        </a>
    </div>
    
    @stack('scripts')
</body>
</html> 