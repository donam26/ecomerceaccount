@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero Banner -->
    <div class="relative">
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Tài khoản Playtogerther chất lượng cao</h1>
                        <p class="text-xl mb-8">Chúng tôi cung cấp các tài khoản game Playtogerther uy tín, giá tốt nhất thị trường, giao dịch an toàn, bảo mật.</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('accounts.index') }}" class="btn-primary px-6 py-3 text-lg">Xem tài khoản</a>
                            <a href="{{ route('about') }}" class="border-2 border-white text-white px-6 py-3 rounded-lg hover:bg-white hover:text-blue-600 transition duration-300 text-lg">Tìm hiểu thêm</a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="{{ asset('images/banner.jpeg') }}" alt="Playtogerther Mobile" class="w-full h-auto rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    <!-- Tài khoản mới nhất -->
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">SPAM KHẮP ĐẢO KAIA</h2>
                <p class="text-gray-600">Cập nhật liên tục các tài khoản mới nhất với nhiều ưu đãi</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($accountCategories as $category)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <a href="{{ route('account.category', $category->slug) }}">
                    @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">{{ $category->name }}</span>
                    </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $category->description }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-medium">Xem tài khoản</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                {{ $category->accounts()->where('status', 'available')->count() }} tài khoản
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('account.categories') }}" class="btn-primary">Xem tất cả danh mục</a>
            </div>
        </div>
    </div>

    <!-- Dịch vụ nổi bật -->
    @if(isset($services) && $services->count() > 0)
    <div class="bg-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Khu vực dịch vụ</h2>
                <p class="text-gray-600">Dịch vụ uy tín từ các ShopBuffsao</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($services as $service)
                <div class="group bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition duration-300">
                    <a href="{{ route('services.show', $service->slug) }}" class="block">
                        <div class="relative h-48 overflow-hidden">
                            @if($service->image)
                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                            <div class="w-full h-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            @endif
                        </div>
                    </a>
                    
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">{{ $service->game->name }}</span>
                            @if($service->is_featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Nổi bật
                            </span>
                            @endif
                        </div>
                        
                        <a href="{{ route('services.show', $service->slug) }}" class="block">
                            <h3 class="font-bold text-gray-800 text-xl mb-2 hover:text-blue-600 transition">{{ $service->name }}</h3>
                        </a>
                        
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($service->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-blue-600 font-semibold">
                                @if($service->packages->count() > 0)
                                Từ {{ number_format($service->packages->min('price'), 0, ',', '.') }}đ
                                @else
                                Liên hệ báo giá
                                @endif
                            </div>
                            <a href="{{ route('services.show', $service->slug) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300">
                                Xem chi tiết
                                <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('services.index') }}" class="btn-primary">Xem tất cả dịch vụ</a>
            </div>
        </div>
    </div>
    @endif

     <!-- Lý do chọn chúng tôi -->
     <div class="bg-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Tại sao chọn chúng tôi?</h2>
                <p class="text-gray-600">Chúng tôi cam kết mang đến cho bạn trải nghiệm mua tài khoản game tốt nhất</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Uy tín hàng đầu</h3>
                    <p class="text-gray-600">Chúng tôi cam kết cung cấp tài khoản chất lượng, đúng như mô tả, mang đến sự hài lòng cho khách hàng.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">An toàn & Bảo mật</h3>
                    <p class="text-gray-600">Giao dịch an toàn, bảo mật thông tin khách hàng tuyệt đối, thanh toán đa dạng qua nhiều hình thức.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-4 mx-auto w-16 h-16 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hỗ trợ 24/7</h3>
                    <p class="text-gray-600">Đội ngũ hỗ trợ chuyên nghiệp, luôn sẵn sàng giải đáp mọi thắc mắc và hỗ trợ bạn khi cần.</p>
                </div>
            </div>
        </div>
    </div>
@endsection 