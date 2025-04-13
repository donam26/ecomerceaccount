<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Dashboard Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 bg-gray-800">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <!-- Logo -->
                        <div class="flex items-center flex-shrink-0 px-4 mb-5">
                            <a href="{{ route('admin.dashboard') }}" class="logo-text">Admin Panel</a>
                        </div>
                        
                        <!-- Menu -->
                        <nav class="mt-5 flex-1 px-2 space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.dashboard') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>
                            
                            <a href="{{ route('admin.games.index') }}" class="{{ request()->routeIs('admin.games.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.games.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Quản lý game
                            </a>
                            
                            <!-- Dropdown: Quản lý tài khoản -->
                            <div x-data="{ open: {{ request()->routeIs('admin.accounts.*') || request()->routeIs('admin.account_categories.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="flex-1">Quản lý tài khoản</span>
                                    <svg :class="{'rotate-90': open, 'rotate-0': !open}" class="w-4 h-4 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" class="mt-1 pl-6 space-y-1">
                                    <a href="{{ route('admin.account_categories.index') }}" class="{{ request()->routeIs('admin.account_categories.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Danh mục tài khoản</span>
                                    </a>
                                    <a href="{{ route('admin.accounts.index') }}" class="{{ request()->routeIs('admin.accounts.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Tài khoản</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Dropdown: Quản lý đơn hàng -->
                            <div x-data="{ open: {{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span class="flex-1">Quản lý đơn hàng</span>
                                    <svg :class="{'rotate-90': open, 'rotate-0': !open}" class="w-4 h-4 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" class="mt-1 pl-6 space-y-1">
                                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Đơn hàng tài khoản</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Dropdown: Quản lý dịch vụ -->
                            <div x-data="{ open: {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.topup.*') || request()->routeIs('admin.boosting.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="flex-1">Quản lý dịch vụ</span>
                                    <svg :class="{'rotate-90': open, 'rotate-0': !open}" class="w-4 h-4 transform transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" class="mt-1 pl-6 space-y-1">
                                    <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Dịch vụ</span>
                                    </a>
                                    <a href="{{ route('admin.services.orders.index') }}" class="{{ request()->routeIs('admin.services.orders.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Đơn hàng dịch vụ</span>
                                    </a>
                                    <a href="{{ route('admin.topup.index') }}" class="{{ request()->routeIs('admin.topup.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <span class="truncate">Dịch vụ nạp hộ</span>
                                    </a>
                                </div>
                            </div>
                            
                            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.users.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Quản lý người dùng
                            </a>
                        </nav>
                    </div>
                    
                    <!-- User profile -->
                    <div class="flex-shrink-0 flex bg-gray-700 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div>
                                    <div class="h-9 w-9 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 text-sm font-medium uppercase">
                                        {{ auth()->user()->name[0] }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-gray-300 hover:text-white">
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="md:hidden bg-gray-800 text-white p-4 flex justify-between items-center w-full">
            <a href="{{ route('admin.dashboard') }}" class="font-bold text-xl">Admin</a>
            <button type="button" class="mobile-menu-button">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
        
        <!-- Mobile menu (hidden by default) -->
        <div class="md:hidden mobile-menu hidden bg-gray-800 w-full absolute z-10" x-data="{ accountsOpen: false, ordersOpen: false, servicesOpen: false }">
            <nav class="px-4 py-2">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Dashboard</a>
                <a href="{{ route('admin.games.index') }}" class="{{ request()->routeIs('admin.games.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Quản lý game</a>
                
                <!-- Mobile: Quản lý tài khoản -->
                <div>
                    <button @click="accountsOpen = !accountsOpen" class="flex items-center justify-between w-full px-4 py-2 text-white">
                        <span>Quản lý tài khoản</span>
                        <svg :class="{'rotate-90': accountsOpen, 'rotate-0': !accountsOpen}" class="w-4 h-4 transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="accountsOpen" class="pl-6">
                        <a href="{{ route('admin.account_categories.index') }}" class="{{ request()->routeIs('admin.account_categories.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Danh mục tài khoản</a>
                        <a href="{{ route('admin.accounts.index') }}" class="{{ request()->routeIs('admin.accounts.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Tài khoản</a>
                    </div>
                </div>
                
                <!-- Mobile: Quản lý đơn hàng -->
                <div>
                    <button @click="ordersOpen = !ordersOpen" class="flex items-center justify-between w-full px-4 py-2 text-white">
                        <span>Quản lý đơn hàng</span>
                        <svg :class="{'rotate-90': ordersOpen, 'rotate-0': !ordersOpen}" class="w-4 h-4 transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="ordersOpen" class="pl-6">
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Đơn hàng tài khoản</a>
                    </div>
                </div>
                
                <!-- Mobile: Quản lý dịch vụ -->
                <div>
                    <button @click="servicesOpen = !servicesOpen" class="flex items-center justify-between w-full px-4 py-2 text-white">
                        <span>Quản lý dịch vụ</span>
                        <svg :class="{'rotate-90': servicesOpen, 'rotate-0': !servicesOpen}" class="w-4 h-4 transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="servicesOpen" class="pl-6">
                        <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Dịch vụ</a>
                        <a href="{{ route('admin.services.orders.index') }}" class="{{ request()->routeIs('admin.services.orders.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Đơn hàng dịch vụ</a>
                        <a href="{{ route('admin.topup.index') }}" class="{{ request()->routeIs('admin.topup.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Dịch vụ nạp hộ</a>
                    </div>
                </div>
                
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-900' : '' }} block px-4 py-2 text-white rounded-md">Quản lý người dùng</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 