@extends('layouts.app')

@section('title', 'Nạp tiền vào ví')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Nạp tiền vào ví</h1>
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại ví
            </a>
        </div>
        
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
        @endif
        
        <!-- Thông tin số dư -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Số dư hiện tại</h2>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ</div>
            </div>
        </div>
        
        <!-- Form nạp tiền -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Nhập số tiền cần nạp</h2>
            </div>
            
            <div class="p-6">
                <form action="{{ route('wallet.deposit.process') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="amount" id="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0" min="10000" step="10000" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">VNĐ</span>
                            </div>
                        </div>
                        @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Số tiền tối thiểu: 10.000 VNĐ</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="block text-sm font-medium text-gray-700 mb-3">Chọn mệnh giá nhanh</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="50000">
                                50.000 VNĐ
                            </button>
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="100000">
                                100.000 VNĐ
                            </button>
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="200000">
                                200.000 VNĐ
                            </button>
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="300000">
                                300.000 VNĐ
                            </button>
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="500000">
                                500.000 VNĐ
                            </button>
                            <button type="button" class="amount-preset px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-amount="1000000">
                                1.000.000 VNĐ
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="block text-sm font-medium text-gray-700 mb-3">Phương thức thanh toán</h3>
                        <div class="border border-gray-300 rounded-md p-4 flex items-center">
                            <input id="payment-vnpay" name="payment_method" type="radio" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <label for="payment-vnpay" class="ml-3 block text-sm font-medium text-gray-700">
                                <span class="flex items-center">
                                    <span class="text-base font-medium text-gray-900 mr-2">VNPay</span>
                                    <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694424446384.png" alt="VNPay" class="h-8">
                                </span>
                                <span class="text-sm text-gray-500 mt-1 block">Thanh toán qua VNPay (ATM, Thẻ quốc tế, QR Code,...)</span>
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Tiếp tục
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
        // Xử lý nút chọn mệnh giá nhanh
        const presetButtons = document.querySelectorAll('.amount-preset');
        const amountInput = document.getElementById('amount');
        
        presetButtons.forEach(button => {
            button.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                amountInput.value = amount;
                
                // Xóa trạng thái active của tất cả các nút
                presetButtons.forEach(btn => {
                    btn.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                    btn.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
                });
                
                // Thêm trạng thái active cho nút được chọn
                this.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
                this.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-700');
            });
        });
    });
</script>
@endpush

@endsection 