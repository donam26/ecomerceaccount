<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ShopBuffsao - Web mua bán tài khoản game uy tín, chất lượng cao">
    <meta name="keywords" content="mua tài khoản game, bán tài khoản, game online, nạp game, dịch vụ game">

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
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <style>
        /* Custom styles */
        .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        }
        .floating-social {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .floating-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        .floating-social a:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(0,0,0,0.2);
        }
        .facebook-btn {
            background: #1877F2;
        }
        .zalo-btn {
            background: #0068ff;
        }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            transform-origin: top;
            transform: scale(0.95);
            opacity: 0;
            visibility: hidden;
            transition: transform 0.2s ease, opacity 0.2s ease, visibility 0.2s ease;
        }
        .dropdown-menu.show {
            transform: scale(1);
            opacity: 1;
            visibility: visible;
        }
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
        }
        .mobile-menu {
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .mobile-menu.show {
            transform: translateY(0);
            opacity: 1;
        }
        .nav-item {
            position: relative;
        }
        .badge-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Header/Navbar -->
        <header class="sticky top-0 z-50">
            <nav class="gradient-bg text-white shadow-md">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                                <!-- Logo tùy chỉnh nếu có -->
                                <span class="text-xl font-bold tracking-tight">ShopBuffsao</span>
                            </a>
                        </div>
                        
                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex md:items-center md:space-x-6">
                            <a href="{{ route('home') }}" class="nav-link px-2 py-2 text-white hover:text-gray-100 font-medium {{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
                            <a href="{{ route('accounts.index') }}" class="nav-link px-2 py-2 text-white hover:text-gray-100 font-medium {{ request()->routeIs('accounts.*') ? 'active' : '' }}">Tài khoản</a>
                            <a href="{{ route('services.index') }}" class="nav-link px-2 py-2 text-white hover:text-gray-100 font-medium {{ request()->routeIs('services.*') ? 'active' : '' }}">Dịch vụ</a>
                            <a href="{{ route('topup.index') }}" class="nav-link px-2 py-2 text-white hover:text-gray-100 font-medium {{ request()->routeIs('topup.*') ? 'active' : '' }}">Nạp hộ</a>
                            <a href="{{ route('contact') }}" class="nav-link px-2 py-2 text-white hover:text-gray-100 font-medium {{ request()->routeIs('contact') ? 'active' : '' }}">Liên hệ</a>
                        </div>
                        
                        <!-- Search -->
                        <div class="hidden md:flex items-center">
                            <form action="{{ route('accounts.search') }}" method="GET" class="relative">
                                <input type="text" name="keyword" placeholder="Tìm kiếm tài khoản..." 
                                    class="w-64 rounded-full py-2 px-4 pr-10 text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 border-0">
                                <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-600">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- User Menu (Desktop) -->
                        <div class="hidden md:flex items-center space-x-4">
                            @guest
                                <a href="{{ route('login') }}" class="text-white hover:text-gray-200 font-medium">Đăng nhập</a>
                                <a href="{{ route('register') }}" class="bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2 rounded-lg font-medium transition duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-md">Đăng ký</a>
                            @else
                                <div class="relative" id="userDropdown">
                                    <div class="flex items-center space-x-3 cursor-pointer">
                                        <!-- Wallet balance -->
                                        <a href="{{ route('wallet.index') }}" class="flex items-center px-3 py-1.5 bg-indigo-700 rounded-lg hover:bg-indigo-800 transition">
                                            <i class="bi bi-wallet2 mr-2"></i>
                                            <span>{{ Auth::user()->wallet ? number_format(Auth::user()->wallet->balance, 0, ',', '.') : 0 }}đ</span>
                                        </a>
                                        
                                        <div class="flex items-center space-x-1">
                                            <span class="text-sm">{{ Auth::user()->name }}</span>
                                            <i class="bi bi-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- User Dropdown Menu -->
                                    <div class="dropdown-menu absolute right-0 mt-2 w-48 py-2 bg-white rounded-lg shadow-lg text-gray-700 z-10">
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:text-indigo-600 transition">
                                                <i class="bi bi-speedometer2 mr-2"></i>Quản trị viên
                                            </a>
                                            <hr class="my-1 border-gray-200">
                                        @endif
                                        
                                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:text-indigo-600 transition">
                                            <i class="bi bi-person mr-2"></i>Thông tin tài khoản
                                        </a>
                                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:text-indigo-600 transition">
                                            <i class="bi bi-bag mr-2"></i>Đơn hàng tài khoản
                                        </a>
                                        <a href="{{ route('services.my_orders') }}" class="block px-4 py-2 text-sm hover:bg-gray-100 hover:text-indigo-600 transition">
                                            <i class="bi bi-clock-history mr-2"></i>Lịch sử dịch vụ
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 hover:text-red-600 transition">
                                                <i class="bi bi-box-arrow-right mr-2"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endguest
                        </div>
                        
                        <!-- Mobile Menu Button -->
                        <div class="flex md:hidden">
                            <button type="button" id="mobileMenuButton" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-indigo-700 transition">
                                <i class="bi bi-list text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Menu -->
                <div id="mobileMenu" class="mobile-menu hidden md:hidden">
                    <div class="px-4 py-2 space-y-1 border-t border-indigo-700">
                        <!-- Mobile Search -->
                        <div class="pb-2">
                            <form action="{{ route('accounts.search') }}" method="GET" class="relative">
                                <input type="text" name="keyword" placeholder="Tìm kiếm tài khoản..." 
                                    class="w-full rounded-full py-2 px-4 pr-10 text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 border-0">
                                <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-600">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Mobile Navigation Links -->
                        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('home') ? 'bg-indigo-700 font-medium' : 'hover:bg-indigo-700' }} transition">
                            <i class="bi bi-house mr-2"></i>Trang chủ
                        </a>
                        <a href="{{ route('accounts.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('accounts.*') ? 'bg-indigo-700 font-medium' : 'hover:bg-indigo-700' }} transition">
                            <i class="bi bi-person-circle mr-2"></i>Tài khoản
                        </a>
                        <a href="{{ route('services.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('services.*') ? 'bg-indigo-700 font-medium' : 'hover:bg-indigo-700' }} transition">
                            <i class="bi bi-gear mr-2"></i>Dịch vụ
                        </a>
                        <a href="{{ route('topup.index') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('topup.*') ? 'bg-indigo-700 font-medium' : 'hover:bg-indigo-700' }} transition">
                            <i class="bi bi-currency-dollar mr-2"></i>Nạp hộ
                        </a>
                        <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-white {{ request()->routeIs('contact') ? 'bg-indigo-700 font-medium' : 'hover:bg-indigo-700' }} transition">
                            <i class="bi bi-envelope mr-2"></i>Liên hệ
                        </a>
                        
                        <div class="pt-2 border-t border-indigo-700 mt-2">
                            @guest
                                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-white hover:bg-indigo-700 transition">
                                    <i class="bi bi-box-arrow-in-right mr-2"></i>Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" class="block mt-2 px-3 py-2 rounded-md bg-white text-indigo-600 hover:bg-gray-100 font-medium text-center transition">
                                    Đăng ký
                                </a>
                            @else
                                <!-- Mobile Wallet -->
                                <a href="{{ route('wallet.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md bg-indigo-700 text-white mb-2">
                                    <div class="flex items-center">
                                        <i class="bi bi-wallet2 mr-2"></i>
                                        <span>Số dư</span>
                                    </div>
                                    <span class="font-semibold">{{ Auth::user()->wallet ? number_format(Auth::user()->wallet->balance, 0, ',', '.') : 0 }}đ</span>
                                </a>
                                
                                <!-- Mobile User Menu -->
                                <a href="{{ route('profile.index') }}" class="block px-3 py-2 rounded-md text-white hover:bg-indigo-700 transition">
                                    <i class="bi bi-person mr-2"></i>Thông tin tài khoản
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-white hover:bg-indigo-700 transition">
                                    <i class="bi bi-bag mr-2"></i>Đơn hàng tài khoản
                                </a>
                                <a href="{{ route('services.my_orders') }}" class="block px-3 py-2 rounded-md text-white hover:bg-indigo-700 transition">
                                    <i class="bi bi-clock-history mr-2"></i>Lịch sử dịch vụ
                                </a>
                                <a href="{{ route('wallet.deposit') }}" class="block px-3 py-2 rounded-md bg-green-600 text-white font-medium hover:bg-green-700 mt-2 transition text-center">
                                    <i class="bi bi-plus-circle mr-2"></i>Nạp tiền
                                </a>
                                
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-white hover:bg-indigo-700 mt-2 transition">
                                        <i class="bi bi-speedometer2 mr-2"></i>Quản trị viên
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-white hover:bg-red-600 transition">
                                        <i class="bi bi-box-arrow-right mr-2"></i>Đăng xuất
                                    </button>
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Breadcrumbs -->
        @hasSection('breadcrumbs')
            <div class="bg-white shadow-sm border-b">
                <div class="container mx-auto px-4 py-2 text-sm">
                    @yield('breadcrumbs')
                </div>
            </div>
        @endif
        
        <!-- Flash Messages -->
        <div class="toast-container" id="toastContainer">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p>{{ session('success') }}</p>
                        </div>
                        <button class="ml-auto text-green-500 hover:text-green-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p>{{ session('error') }}</p>
                        </div>
                        <button class="ml-auto text-red-500 hover:text-red-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            @endif
            
            @if (session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p>{{ session('warning') }}</p>
                        </div>
                        <button class="ml-auto text-yellow-500 hover:text-yellow-700" onclick="this.parentElement.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Page Content -->
        <main class="flex-grow">
            <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-white pt-12 pb-6">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div data-aos="fade-up" data-aos-delay="100">
                        <h4 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="bi bi-info-circle mr-2"></i>Về chúng tôi
                        </h4>
                        <p class="text-gray-400 mb-4">
                            ShopBuffsao là nơi cung cấp tài khoản game chất lượng, uy tín, với nhiều ưu đãi hấp dẫn.
                        </p>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/people/Shopbuffsao/61574594802771/" target="_blank" class="text-gray-400 hover:text-white transition">
                                <i class="bi bi-facebook text-xl"></i>
                            </a>
                            <a href="https://zalo.me/0876085633" target="_blank" class="text-gray-400 hover:text-white transition">
                                <span class="font-bold">Zalo</span>
                            </a>
                        </div>
                    </div>
                    
                    <div data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="bi bi-link-45deg mr-2"></i>Liên kết nhanh
                        </h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('home') }}" class="hover:text-white transition"><i class="bi bi-chevron-right mr-2 text-xs"></i>Trang chủ</a></li>
                            <li><a href="{{ route('accounts.index') }}" class="hover:text-white transition"><i class="bi bi-chevron-right mr-2 text-xs"></i>Tài khoản</a></li>
                            <li><a href="{{ route('services.index') }}" class="hover:text-white transition"><i class="bi bi-chevron-right mr-2 text-xs"></i>Dịch vụ</a></li>
                            <li><a href="{{ route('topup.index') }}" class="hover:text-white transition"><i class="bi bi-chevron-right mr-2 text-xs"></i>Nạp hộ</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-white transition"><i class="bi bi-chevron-right mr-2 text-xs"></i>Liên hệ</a></li>
                        </ul>
                    </div>
                    
                    <div data-aos="fade-up" data-aos-delay="300">
                        <h4 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="bi bi-telephone mr-2"></i>Liên hệ
                        </h4>
                        <ul class="space-y-3 text-gray-400">
                            <li class="flex items-start">
                                <i class="bi bi-geo-alt mt-1 mr-3 text-indigo-400"></i>
                                <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                            </li>
                            <li class="flex items-center">
                                <i class="bi bi-telephone mr-3 text-indigo-400"></i>
                                <a href="tel:0876085633" class="hover:text-white transition">0876085633</a>
                            </li>
                            <li class="flex items-center">
                                <i class="bi bi-envelope mr-3 text-indigo-400"></i>
                                <a href="mailto:shopbuffsao@gmail.com" class="hover:text-white transition">shopbuffsao@gmail.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-10 pt-6 text-center text-gray-500 text-sm">
                    <p>&copy; {{ date('Y') }} ShopBuffsao. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Floating Social Buttons -->
    <div class="floating-social">
        <a href="https://www.facebook.com/people/Shopbuffsao/61574594802771/" target="_blank" class="facebook-btn" data-aos="fade-left" data-aos-delay="100">
            <i class="bi bi-facebook text-xl"></i>
        </a>
        <a href="https://zalo.me/0876085633" target="_blank" class="zalo-btn" data-aos="fade-left" data-aos-delay="200">
            <span class="font-bold text-sm">Zalo</span>
        </a>
        <button id="scrollToTop" class="bg-gray-800 text-white hover:bg-gray-700 transition" data-aos="fade-left" data-aos-delay="300">
            <i class="bi bi-arrow-up"></i>
        </button>
    </div>
    
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Mobile menu
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenu.classList.toggle('show');
                });
            }
            
            // User dropdown
            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown) {
                const dropdownMenu = userDropdown.querySelector('.dropdown-menu');
                
                userDropdown.addEventListener('click', function(e) {
                    dropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (userDropdown && !userDropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
            
            // Auto-hide flash messages
            setTimeout(function() {
                const toasts = document.querySelectorAll('.toast-container > div');
                toasts.forEach(toast => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                });
            }, 5000);
            
            // Scroll to top
            const scrollToTopButton = document.getElementById('scrollToTop');
            if (scrollToTopButton) {
                scrollToTopButton.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
                
                // Show/hide scroll to top button
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        scrollToTopButton.style.opacity = '1';
                    } else {
                        scrollToTopButton.style.opacity = '0';
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 