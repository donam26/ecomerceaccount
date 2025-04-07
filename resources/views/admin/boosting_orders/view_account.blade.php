@extends('layouts.admin')

@section('title', 'Thông tin tài khoản game')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center">
        <h2 class="my-6 text-2xl font-semibold text-gray-700">
            Thông tin tài khoản game
        </h2>
    </div>

    <!-- Nút quay lại -->
    <div class="mb-6">
        <a href="{{ route('admin.boosting_orders.show', $order->id) }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Quay lại đơn hàng</span>
        </a>
    </div>

    <!-- Thông báo an ninh -->
    <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
        <span class="font-bold">Thông tin bảo mật!</span> Các thông tin tài khoản này cần được bảo mật và chỉ phân công cho nhân viên phụ trách dịch vụ này.
    </div>

    <!-- Thông tin đơn hàng -->
    <div class="grid gap-6 mb-8 md:grid-cols-2">
        <!-- Thông tin cơ bản đơn hàng -->
        <div class="min-w-0 p-4 bg-white rounded-lg shadow-md">
            <h4 class="mb-4 font-semibold text-gray-600 border-b pb-2">Thông tin đơn hàng</h4>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Mã đơn hàng:</p>
                <p class="text-md font-semibold">{{ $order->order_number }}</p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Dịch vụ:</p>
                <p class="text-md font-semibold">{{ $order->service->name }}</p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Game:</p>
                <p class="text-md">{{ $order->service->game->name }}</p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Khách hàng:</p>
                <p class="text-md">{{ $order->user->name }}</p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Trạng thái:</p>
                <div>
                    @if($order->status == 'pending')
                    <span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">
                        Chờ thanh toán
                    </span>
                    @elseif($order->status == 'paid')
                    <span class="px-2 py-1 font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                        Đã thanh toán
                    </span>
                    @elseif($order->status == 'processing')
                    <span class="px-2 py-1 font-semibold leading-tight text-purple-700 bg-purple-100 rounded-full">
                        Đang xử lý
                    </span>
                    @elseif($order->status == 'completed')
                    <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                        Hoàn thành
                    </span>
                    @elseif($order->status == 'cancelled')
                    <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                        Đã hủy
                    </span>
                    @endif
                </div>
            </div>
            
            @if($order->assigned_to)
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Nhân viên phụ trách:</p>
                <p class="text-md font-medium">{{ $order->assignedTo->name }}</p>
            </div>
            @endif
        </div>
        
        <!-- Thông tin tài khoản game của khách hàng -->
        <div class="min-w-0 p-4 bg-white rounded-lg shadow-md">
            <h4 class="mb-4 font-semibold text-gray-600 border-b pb-2">Thông tin tài khoản game</h4>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Tên tài khoản:</p>
                <p class="text-md font-semibold">{{ $order->game_username }}</p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Mật khẩu:</p>
                <div class="relative">
                    <input id="password-field" type="password" value="{{ $order->game_password }}" readonly
                        class="block w-full text-md border-gray-300 rounded-md bg-gray-100 cursor-text">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button id="toggle-password" type="button">
                            <svg id="eye-open" class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg id="eye-closed" class="h-5 w-5 text-gray-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <button id="copy-password" class="text-sm text-gray-600 hover:text-purple-600">
                        <svg class="h-4 w-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        Sao chép mật khẩu
                    </button>
                    <span id="copy-success" class="text-sm text-green-600 ml-2 hidden">Đã sao chép!</span>
                </div>
            </div>
            
            @if($order->additional_info)
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-1">Thông tin bổ sung:</p>
                <div class="bg-gray-100 p-3 rounded-md">
                    <p class="text-md whitespace-pre-line">{{ $order->additional_info }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Ghi chú và trạng thái -->
    <div class="min-w-0 p-4 bg-white rounded-lg shadow-md mb-8">
        <h4 class="mb-4 font-semibold text-gray-600 border-b pb-2">Ghi chú nội bộ</h4>
        
        <form action="{{ route('admin.boosting_orders.update-notes', $order->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú công việc</label>
                <textarea id="admin_notes" name="admin_notes" rows="4"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-textarea">{{ $order->admin_notes }}</textarea>
            </div>
            
            <button type="submit"
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring">
                Cập nhật ghi chú
            </button>
        </form>
    </div>
</div>

<script>
    // Hiển thị/ẩn mật khẩu
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password-field');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
        
        togglePassword.addEventListener('click', function () {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
        
        // Sao chép mật khẩu
        const copyPassword = document.getElementById('copy-password');
        const copySuccess = document.getElementById('copy-success');
        
        copyPassword.addEventListener('click', function () {
            passwordField.type = 'text';
            passwordField.select();
            document.execCommand('copy');
            passwordField.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
            
            copySuccess.classList.remove('hidden');
            setTimeout(function () {
                copySuccess.classList.add('hidden');
            }, 2000);
        });
    });
</script>
@endsection 