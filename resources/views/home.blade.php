@extends('layouts.app')

@section('title', 'Trang chủ')

@section('breadcrumbs')
<div class="flex items-center text-sm">
    <a href="{{ route('home') }}" class="text-indigo-600 font-medium">Trang chủ</a>
</div>
@endsection

@section('content')
    <!-- Hero Banner -->
    <div class="relative">
        <div class="bg-gradient-to-r from-indigo-700 to-blue-500 text-white relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>
            
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div data-aos="fade-right">
                        <h1 class="text-4xl md:text-5xl font-extrabold mb-6 animate-fade-in-up leading-tight">
                            Tài khoản Playtogerther <span class="text-yellow-300">chất lượng cao</span>
                        </h1>
                        <p class="text-xl mb-8 text-gray-200 leading-relaxed animate-fade-in-up animate-delay-100">
                            Chúng tôi cung cấp các tài khoản game uy tín, giá tốt nhất thị trường, giao dịch an toàn, bảo mật.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up animate-delay-200">
                            <a href="{{ route('accounts.index') }}" class="btn-primary px-6 py-3 text-lg inline-flex items-center justify-center">
                                <i class="bi bi-controller mr-2"></i>Xem tài khoản
                            </a>
                            <a href="{{ route('about') }}" class="border-2 border-white text-white px-6 py-3 rounded-lg hover:bg-white hover:text-indigo-600 transition duration-300 text-lg flex items-center justify-center">
                                <i class="bi bi-info-circle mr-2"></i>Tìm hiểu thêm
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block" data-aos="fade-left">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-white/10 rounded-2xl blur-xl"></div>
                            <img src="{{ asset('images/banner.jpeg') }}" alt="Playtogerther Mobile" class="w-full h-auto rounded-xl shadow-2xl relative transform hover:scale-105 transition duration-700">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Wave effect -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" class="w-full">
                    <path fill="#f9fafb" fill-opacity="1" d="M0,32L80,42.7C160,53,320,75,480,74.7C640,75,800,53,960,37.3C1120,21,1280,11,1360,5.3L1440,0L1440,100L1360,100C1280,100,1120,100,960,100C800,100,640,100,480,100C320,100,160,100,80,100L0,100Z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Danh mục tài khoản -->
    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center" data-aos="fade-up">
                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium inline-block px-2.5 py-1 rounded-full">DANH MỤC TÀI KHOẢN</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-2 mt-2">SPAM KHẮP ĐẢO KAIA</h2>
                <div class="divider"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Cập nhật liên tục các tài khoản mới nhất với nhiều ưu đãi hấp dẫn</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach($accountCategories as $category)
                <div class="card hover-shadow" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('account.category', $category->slug) }}">
                        @if($category->image)
                            <div class="overflow-hidden rounded-t-xl">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" 
                                    class="w-full h-52 object-cover transition-transform duration-500 hover:scale-110">
                            </div>
                        @else
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-52 flex items-center justify-center rounded-t-xl">
                                <span class="text-white text-2xl font-bold px-4 text-center">{{ $category->name }}</span>
                            </div>
                        @endif
                        <div class="p-5">
                            <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2 h-10">{{ $category->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-indigo-600 font-medium flex items-center">
                                    Xem tài khoản
                                    <i class="bi bi-arrow-right ml-1"></i>
                                </span>
                                <span class="badge badge-blue">
                                    {{ $category->accounts()->where('status', 'available')->count() }} tài khoản
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
            </div>
            
            <div class="mt-12 text-center" data-aos="fade-up">
                <a href="{{ route('account.categories') }}" class="btn-primary px-8 py-3 flex items-center justify-center mx-auto w-auto max-w-xs">
                    <i class="bi bi-grid-3x3-gap mr-2"></i>
                    Xem tất cả danh mục
                </a>
            </div>
        </div>
    </div>

    <!-- Dịch vụ nổi bật -->
    @if(isset($services) && $services->count() > 0)
    <div class="bg-white py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center" data-aos="fade-up">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium inline-block px-2.5 py-1 rounded-full">DỊCH VỤ HÀNG ĐẦU</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-2 mt-2">Khu vực dịch vụ</h2>
                <div class="divider"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Dịch vụ uy tín từ các ShopBuffsao</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($services as $service)
                <div class="group card card-hover-effect" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <a href="{{ route('services.show', $service->slug) }}" class="block">
                        <div class="relative h-48 overflow-hidden rounded-t-xl">
                            @if($service->image)
                                <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                    <i class="bi bi-play-circle text-white text-6xl"></i>
                                </div>
                            @endif
                            <!-- Game tag and featured badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-indigo-600/80 text-white text-xs font-medium px-2.5 py-1 rounded backdrop-blur-sm">
                                    {{ $service->game->name }}
                                </span>
                            </div>
                            @if($service->is_featured)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-yellow-500/80 text-white text-xs font-medium px-2.5 py-1 rounded flex items-center backdrop-blur-sm">
                                        <i class="bi bi-star-fill mr-1"></i>
                                        Nổi bật
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>
                    
                    <div class="p-5">
                        <a href="{{ route('services.show', $service->slug) }}" class="block">
                            <h3 class="font-bold text-gray-800 text-xl mb-2 hover:text-indigo-600 transition">{{ $service->name }}</h3>
                        </a>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2 h-10">{{ Str::limit($service->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-indigo-600 font-semibold">
                                @if($service->packages->count() > 0)
                                    Từ {{ number_format($service->packages->min('price'), 0, ',', '.') }}đ
                                @else
                                    Liên hệ báo giá
                                @endif
                            </div>
                            <a href="{{ route('services.show', $service->slug) }}" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 transition">
                                <span>Chi tiết</span>
                                <i class="bi bi-chevron-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-12 text-center" data-aos="fade-up">
                <a href="{{ route('services.index') }}" class="btn-primary px-8 py-3 flex items-center justify-center mx-auto w-auto max-w-xs">
                    <i class="bi bi-grid-3x3-gap mr-2"></i>
                    Xem tất cả dịch vụ
                </a>
            </div>
        </div>
    </div>
    @endif

     <!-- Lý do chọn chúng tôi -->
     <div class="bg-gradient-to-r from-indigo-700 to-blue-700 text-white py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center" data-aos="fade-up">
                <span class="bg-white/20 text-white text-xs font-medium inline-block px-2.5 py-1 rounded-full backdrop-blur-sm">VÌ SAO CHỌN CHÚNG TÔI</span>
                <h2 class="text-3xl font-bold mb-2 mt-2">Tại sao chọn chúng tôi?</h2>
                <div class="w-16 sm:w-24 h-1 bg-white/20 rounded-full mx-auto my-4"></div>
                <p class="text-gray-200 max-w-2xl mx-auto">Chúng tôi cam kết mang đến cho bạn trải nghiệm mua tài khoản game tốt nhất</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="text-center flex flex-col items-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-white/10 rounded-xl p-6 mb-6 w-20 h-20 flex items-center justify-center backdrop-blur-sm">
                        <i class="bi bi-patch-check text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Uy tín hàng đầu</h3>
                    <p class="text-gray-200">Chúng tôi cam kết cung cấp tài khoản chất lượng, đúng như mô tả, mang đến sự hài lòng cho khách hàng.</p>
                </div>
                
                <div class="text-center flex flex-col items-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white/10 rounded-xl p-6 mb-6 w-20 h-20 flex items-center justify-center backdrop-blur-sm">
                        <i class="bi bi-shield-check text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">An toàn & Bảo mật</h3>
                    <p class="text-gray-200">Giao dịch an toàn, bảo mật thông tin khách hàng tuyệt đối, thanh toán đa dạng qua nhiều hình thức.</p>
                </div>
                
                <div class="text-center flex flex-col items-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white/10 rounded-xl p-6 mb-6 w-20 h-20 flex items-center justify-center backdrop-blur-sm">
                        <i class="bi bi-headset text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Hỗ trợ 24/7</h3>
                    <p class="text-gray-200">Đội ngũ hỗ trợ chuyên nghiệp, luôn sẵn sàng giải đáp mọi thắc mắc và hỗ trợ bạn khi cần.</p>
                </div>
            </div>
            
            <!-- CTA Button -->
            <div class="mt-12 text-center" data-aos="fade-up">
                <a href="{{ route('contact') }}" class="bg-white text-indigo-700 px-8 py-3 rounded-lg font-medium inline-flex items-center hover:bg-gray-100 transition-all transform hover:-translate-y-1 hover:shadow-xl">
                    <i class="bi bi-chat-dots mr-2"></i>
                    Liên hệ với chúng tôi
                </a>
            </div>
        </div>
    </div>
@endsection 