@extends('layouts.app')

@section('title', 'Nhập thông tin tài khoản - ' . $order->service->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Trang chủ
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('boosting.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Dịch vụ cày thuê</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('boosting.my_orders') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Đơn hàng của tôi</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nhập thông tin tài khoản</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Các bước hoàn thành -->
    <div class="mb-10">
        <div class="flex flex-wrap md:flex-nowrap justify-between">
            <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0 text-center md:text-left relative">
                <div class="bg-green-500 text-white rounded-full w-14 h-14 flex items-center justify-center mx-auto md:mx-0 z-10 relative border-2 border-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-2 text-sm font-medium text-green-600">Đặt hàng</div>
                <div class="absolute top-7 left-1/2 w-full h-0.5 bg-green-500 hidden md:block" style="left: 50%; width: 100%;"></div>
            </div>
            
            <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0 text-center relative">
                <div class="bg-green-500 text-white rounded-full w-14 h-14 flex items-center justify-center mx-auto z-10 relative border-2 border-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div class="mt-2 text-sm font-medium text-green-600">Thanh toán</div>
                <div class="absolute top-7 left-1/2 w-full h-0.5 bg-green-500 hidden md:block" style="left: 50%; width: 100%;"></div>
            </div>
            
            <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0 text-center relative">
                <div class="bg-green-500 text-white rounded-full w-14 h-14 flex items-center justify-center mx-auto z-10 relative border-2 border-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="mt-2 text-sm font-medium text-green-600">Thông tin tài khoản</div>
                <div class="absolute top-7 left-1/2 w-full h-0.5 bg-gray-300 hidden md:block" style="left: 50%; width: 100%;"></div>
            </div>
            
            <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0 text-center md:text-right relative">
                <div class="bg-gray-200 text-gray-500 rounded-full w-14 h-14 flex items-center justify-center mx-auto md:ml-auto md:mr-0 z-10 relative border-2 border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-2 text-sm font-medium text-gray-500">Hoàn thành</div>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Thông tin đơn hàng -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 text-white px-4 py-3">
                    <h3 class="font-semibold text-lg">Thông tin đơn hàng #{{ $order->order_number }}</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Dịch vụ</div>
                        <div class="font-semibold text-lg">{{ $order->service->name }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Trò chơi</div>
                        <div class="font-semibold">{{ $order->service->game->name }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Thời gian hoàn thành ước tính</div>
                        <div class="font-semibold">{{ $order->service->estimated_days }} ngày</div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500">Tạm tính:</span>
                            <span class="font-semibold">{{ number_format($order->original_amount, 0, ',', '.') }}đ</span>
                        </div>
                        
                        @if($order->discount > 0)
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500">Giảm giá:</span>
                            <span class="font-semibold text-red-600">-{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between mt-2">
                            <span class="text-gray-500 font-semibold">Thành tiền:</span>
                            <span class="font-bold text-red-600 text-lg">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border-l-4 border-green-500 mt-4 p-4 text-green-700">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Đơn hàng đã được thanh toán thành công!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form nhập thông tin tài khoản -->
        <div class="w-full md:w-2/3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-lg">Nhập thông tin tài khoản game</h3>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-blue-700">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-blue-800 font-medium">Bảo mật thông tin</h4>
                                <p class="text-sm mt-1">Thông tin tài khoản của bạn được mã hóa và chỉ được sử dụng để thực hiện dịch vụ. Chúng tôi cam kết bảo vệ dữ liệu của bạn theo chính sách bảo mật.</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('boosting.account_info.submit', $order->order_number) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="game_username" class="block text-sm font-medium text-gray-700 mb-1">
                                Tên đăng nhập game <span class="text-red-600">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="game_username" name="game_username" value="{{ old('game_username') }}" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md @error('game_username') border-red-500 @enderror"
                                    placeholder="Nhập tên đăng nhập game của bạn">
                                @error('game_username')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="game_password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mật khẩu game <span class="text-red-600">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="password" id="game_password" name="game_password" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md @error('game_password') border-red-500 @enderror"
                                    placeholder="Nhập mật khẩu game của bạn">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" class="toggle-password focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                                @error('game_password')
                                    <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="additional_info" class="block text-sm font-medium text-gray-700 mb-1">
                                Thông tin bổ sung
                            </label>
                            <textarea id="additional_info" name="additional_info" rows="4"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('additional_info') border-red-500 @enderror"
                                placeholder="Cung cấp thêm thông tin như nhân vật, yêu cầu đặc biệt, lưu ý cho nhân viên cày thuê...">{{ old('additional_info') }}</textarea>
                            <div class="mt-1 text-sm text-gray-500">
                                <span class="font-medium">Gợi ý:</span> Nhân vật cần sử dụng, yêu cầu đặc biệt về thời gian, hướng dẫn cụ thể, lưu ý cho nhân viên cày thuê.
                            </div>
                            @error('additional_info')
                                <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="terms" name="terms" type="checkbox" required
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="terms" class="font-medium text-gray-700">
                                        Tôi đồng ý với <a href="#" class="text-blue-600 hover:text-blue-800">Điều khoản dịch vụ</a> và 
                                        cho phép sử dụng thông tin tài khoản cho mục đích thực hiện dịch vụ cày thuê
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Xác nhận thông tin tài khoản
                            </button>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 text-yellow-700">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-yellow-800 font-medium">Lưu ý quan trọng</h4>
                                <p class="text-sm mt-1">Vui lòng đảm bảo thông tin tài khoản chính xác để tránh chậm trễ trong quá trình thực hiện dịch vụ. Sau khi xác nhận, chúng tôi sẽ tiến hành ngay lập tức.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hiển thị/ẩn mật khẩu
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#game_password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Thay đổi icon
                const svg = this.querySelector('svg');
                if (type === 'text') {
                    svg.innerHTML = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />';
                } else {
                    svg.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />';
                }
            });
        }
    });
</script>
@endpush
@endsection 