@extends('layouts.app')

@section('title', 'Nạp tiền vào ví')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Nạp tiền vào ví</h1>
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại ví
            </a>
        </div>
        
        <div class="mb-6 text-right">
            <a href="{{ route('wallet.card.history') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Xem lịch sử nạp thẻ
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
        
        <!-- Tab navigation -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button type="button" class="tab-btn active-tab w-1/2 py-4 px-6 text-center border-b-2 font-medium text-sm" data-tab="tab-transfer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Chuyển khoản
                    </button>
                    <button type="button" class="tab-btn w-1/2 py-4 px-6 text-center border-b-2 font-medium text-sm" data-tab="tab-card">
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
                        
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Tiếp tục
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
                            <select id="telco" name="telco" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                <option value="">-- Chọn nhà mạng --</option>
                                <option value="VIETTEL">Viettel</option>
                                <option value="MOBIFONE">Mobifone</option>
                                <option value="VINAPHONE">Vinaphone</option>
                                <option value="VIETNAMOBILE">Vietnamobile</option>
                            </select>
                            @error('telco')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="card_amount" class="block text-sm font-medium text-gray-700 mb-1">Mệnh giá thẻ</label>
                            <select id="card_amount" name="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                <option value="">-- Chọn mệnh giá --</option>
                                <option value="10000">10.000 VNĐ</option>
                                <option value="20000">20.000 VNĐ</option>
                                <option value="50000">50.000 VNĐ</option>
                                <option value="100000">100.000 VNĐ</option>
                                <option value="200000">200.000 VNĐ</option>
                                <option value="500000">500.000 VNĐ</option>
                            </select>
                            @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="serial" class="block text-sm font-medium text-gray-700 mb-1">Số Serial</label>
                            <input type="text" name="serial" id="serial" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nhập số serial thẻ cào" required>
                            @error('serial')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã thẻ</label>
                            <input type="text" name="code" id="code" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nhập mã thẻ cào" required>
                            @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-md mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Lưu ý quan trọng</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>- Thẻ cào được nạp sẽ bị trừ 15-30% giá trị tùy loại thẻ.</p>
                                        <p>- Vui lòng kiểm tra kỹ thông tin thẻ trước khi nạp.</p>
                                        <p>- Mỗi thẻ chỉ có thể nạp một lần.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Nạp thẻ ngay
                            </button>
                        </div>
                    </form>
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
                    btn.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                    btn.classList.add('bg-white', 'border-gray-300', 'text-gray-700');
                });
                
                // Thêm trạng thái active cho nút được chọn
                this.classList.remove('bg-white', 'border-gray-300', 'text-gray-700');
                this.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-700');
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
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                });
                
                this.classList.add('active-tab', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
        });
    });
</script>
<style>
    .active-tab {
        border-bottom-color: #3b82f6;
        color: #2563eb;
    }
    
    .tab-btn:not(.active-tab) {
        border-bottom-color: transparent;
        color: #6b7280;
    }
</style>
@endpush

@endsection 