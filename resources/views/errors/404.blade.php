@extends('layouts.app')

@section('title', 'Không tìm thấy trang - 404')

@section('content')
<div class="bg-gray-50 min-h-[70vh] flex items-center">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-center gap-8">
                <div class="w-full md:w-1/2">
                    <div class="text-center md:text-left">
                        <h1 class="text-7xl md:text-9xl font-bold text-blue-600 mb-4">404</h1>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Trang không tồn tại</h2>
                        <p class="text-gray-600 mb-8">Rất tiếc, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
                        <div class="flex flex-col md:flex-row gap-4 justify-center md:justify-start">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Về trang chủ
                            </a>
                            <a href="{{ route('boosting.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Xem dịch vụ cày thuê
                            </a>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <img src="{{ asset('images/error-404.svg') }}" alt="Lỗi 404" class="w-full max-w-md mx-auto" onerror="this.onerror=null;this.src='https://illustrations.popsy.co/amber/falling.svg';">
                </div>
            </div>

            <div class="mt-16 border-t border-gray-200 pt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bạn có thể thử:</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-600">
                            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Truy cập trang chủ</a> để tìm sản phẩm
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-600">
                            <a href="{{ route('boosting.index') }}" class="text-blue-600 hover:text-blue-800">Xem tất cả dịch vụ cày thuê</a>
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-600">
                            <a href="{{ route('games.index') }}" class="text-blue-600 hover:text-blue-800">Khám phá danh mục game</a>
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-600">
                            <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">Liên hệ với chúng tôi</a> để được hỗ trợ
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 