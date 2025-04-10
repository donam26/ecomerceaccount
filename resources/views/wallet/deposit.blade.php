@extends('layouts.app')

@section('title', 'Nạp tiền vào ví')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Nạp tiền vào ví</h1>
                    <p class="mt-2 text-gray-600">Nạp tiền an toàn và nhanh chóng</p>
                </div>
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại ví
                </a>
            </div>
            
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-gray-600">Chọn phương thức nạp tiền</span>
                </div>
                <a href="{{ route('wallet.card.history') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Xem lịch sử nạp thẻ
                </a>
            </div>
            
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Thông tin số dư -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl overflow-hidden shadow-lg mb-8 text-white">
                <div class="relative p-6">
                    <div class="absolute top-0 right-0 -mt-6 -mr-6 w-24 h-24 rounded-full bg-white opacity-10"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 rounded-full bg-white opacity-10"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-lg font-medium text-white opacity-90">Số dư hiện tại</h2>
                        <div class="mt-2 text-4xl font-bold">{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ</div>
                        <div class="mt-3 text-sm opacity-80">Số dư khả dụng để thanh toán dịch vụ</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-3 text-sm text-center">
                    <span class="opacity-90">Lịch sử giao dịch được cập nhật tức thì</span>
                </div>
            </div>
            
            <!-- Tab navigation -->
            <div class="bg-white rounded-xl overflow-hidden shadow-lg mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" class="tab-btn active-tab w-1/2 py-4 px-6 text-center border-b-2 font-medium transition-all duration-200" data-tab="tab-transfer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Chuyển khoản ngân hàng
                        </button>
                        <button type="button" class="tab-btn w-1/2 py-4 px-6 text-center border-b-2 font-medium transition-all duration-200" data-tab="tab-card">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 104 0V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01" />
                            </svg>
                            Nạp thẻ cào
                        </button>
                    </nav>
                </div>
                
                <!-- Tab content -->
                <div class="tab-content">
                    <!-- Tab 1: Chuyển khoản -->
                    <div id="tab-transfer" class="tab-pane active p-6">
                        <form action="{{ route('wallet.deposit.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="bank_transfer">
                            <input type="hidden" name="deposit_code" value="{{ $depositCode }}">
                            
                            <div class="mb-6">
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền cần nạp</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="amount" id="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 pr-16 py-3 sm:text-lg border-gray-300 rounded-lg" placeholder="0" min="10000" step="10000" required>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-lg font-medium">VNĐ</span>
                                    </div>
                                </div>
                                @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Số tiền tối thiểu: 10.000 VNĐ</p>
                            </div>
                            
                            <div class="mb-8">
                                <h3 class="block text-sm font-medium text-gray-700 mb-3">Chọn mệnh giá nhanh</h3>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="50000">
                                        50.000 VNĐ
                                    </button>
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="100000">
                                        100.000 VNĐ
                                    </button>
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="200000">
                                        200.000 VNĐ
                                    </button>
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="300000">
                                        300.000 VNĐ
                                    </button>
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="500000">
                                        500.000 VNĐ
                                    </button>
                                    <button type="button" class="amount-preset px-3 py-3 border border-gray-300 rounded-lg text-sm font-medium hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all duration-200" data-amount="1000000">
                                        1.000.000 VNĐ
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    Tiếp tục nạp tiền
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Tab 2: Nạp thẻ cào -->
                    <div id="tab-card" class="tab-pane hidden p-6">
                        <form action="{{ route('wallet.deposit.card') }}" method="POST">
                            @csrf
                            
                            <div class="mb-6">
                                <label for="telco" class="block text-sm font-medium text-gray-700 mb-1">Chọn nhà mạng</label>
                                <div class="relative">
                                    <select id="telco" name="telco" class="appearance-none focus:ring-blue-500 focus:border-blue-500 block w-full py-3 pl-4 pr-10 sm:text-lg border-gray-300 rounded-lg" required>
                                        <option value="">-- Chọn nhà mạng --</option>
                                        <option value="VIETTEL">Viettel</option>
                                        <option value="MOBIFONE">Mobifone</option>
                                        <option value="VINAPHONE">Vinaphone</option>
                                        <option value="VIETNAMOBILE">Vietnamobile</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                @error('telco')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="card_amount" class="block text-sm font-medium text-gray-700 mb-1">Mệnh giá thẻ</label>
                                <div class="relative">
                                    <select id="card_amount" name="amount" class="appearance-none focus:ring-blue-500 focus:border-blue-500 block w-full py-3 pl-4 pr-10 sm:text-lg border-gray-300 rounded-lg" required>
                                        <option value="">-- Chọn mệnh giá --</option>
                                        <option value="10000">10.000 VNĐ</option>
                                        <option value="20000">20.000 VNĐ</option>
                                        <option value="50000">50.000 VNĐ</option>
                                        <option value="100000">100.000 VNĐ</option>
                                        <option value="200000">200.000 VNĐ</option>
                                        <option value="500000">500.000 VNĐ</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="serial" class="block text-sm font-medium text-gray-700 mb-1">Số Serial</label>
                                <input type="text" name="serial" id="serial" class="focus:ring-blue-500 focus:border-blue-500 block w-full py-3 sm:text-lg border-gray-300 rounded-lg" placeholder="Nhập số serial thẻ cào" required>
                                @error('serial')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã thẻ</label>
                                <input type="text" name="code" id="code" class="focus:ring-blue-500 focus:border-blue-500 block w-full py-3 sm:text-lg border-gray-300 rounded-lg" placeholder="Nhập mã thẻ cào" required>
                                @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg mb-8">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-semibold text-yellow-800">Lưu ý quan trọng</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p class="mb-1">• Thẻ cào được nạp sẽ bị trừ 15-30% giá trị tùy loại thẻ.</p>
                                            <p class="mb-1">• Vui lòng kiểm tra kỹ thông tin thẻ trước khi nạp.</p>
                                            <p>• Mỗi thẻ chỉ có thể nạp một lần.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Nạp thẻ ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
                    btn.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-gray-300', 'text-gray-700');
                });
                
                // Thêm trạng thái active cho nút được chọn
                this.classList.remove('border-gray-300', 'text-gray-700');
                this.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-600');
            });
        });
        
        // Xử lý chuyển tab
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Ẩn tất cả các tab
                tabPanes.forEach(pane => {
                    pane.classList.add('hidden');
                });
                
                // Hiển thị tab được chọn
                document.getElementById(tabId).classList.remove('hidden');
                
                // Cập nhật trạng thái active cho các nút tab
                tabButtons.forEach(btn => {
                    btn.classList.remove('active-tab', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700');
                });
                
                this.classList.add('active-tab', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700');
            });
        });
    });
</script>
<style>
    .active-tab {
        border-bottom-color: #3b82f6;
        color: #2563eb;
        font-weight: 600;
    }
    
    .tab-btn:not(.active-tab) {
        border-bottom-color: transparent;
        color: #6b7280;
    }
    
    /* Hiệu ứng nút chọn mệnh giá */
    .amount-preset.active {
        background-color: #eff6ff;
        border-color: #3b82f6;
        color: #2563eb;
    }
    
    /* Hiệu ứng input focus */
    input:focus, select:focus {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
</style>
@endpush

@endsection 