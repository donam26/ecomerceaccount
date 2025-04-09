@extends('layouts.app')

@section('title', 'Đang xử lý thẻ cào')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Đang xử lý thẻ cào</h1>
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại ví
            </a>
        </div>
        
        <div id="status-message" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
            <p class="font-medium">Đang xử lý thẻ cào của bạn</p>
            <p>Hệ thống đang kiểm tra thẻ cào, vui lòng đợi trong giây lát.</p>
        </div>
        
        <!-- Thông tin thẻ cào -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin thẻ cào</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Nhà mạng:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $cardDeposit->getTelcoNameAttribute() }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Mệnh giá:</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($cardDeposit->amount, 0, ',', '.') }} VNĐ</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Số tiền nhận được:</span>
                        <span id="actual-amount" class="text-sm font-medium text-gray-900">{{ number_format($cardDeposit->actual_amount, 0, ',', '.') }} VNĐ</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Mã giao dịch:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $cardDeposit->request_id }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Thời gian:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $cardDeposit->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Trạng thái:</span>
                        <span id="status" class="text-sm font-medium">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Đang xử lý
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trạng thái xử lý -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div id="loading-spinner" class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                </div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-500">Hệ thống đang xử lý thẻ cào của bạn.</p>
                    <p class="text-sm text-gray-500">Vui lòng không đóng trang này.</p>
                </div>
            </div>
        </div>
        
        <!-- Nút hành động -->
        <div class="flex flex-col space-y-4">
            <a id="success-button" href="{{ route('wallet.index') }}" class="w-full hidden flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Quay lại ví
            </a>
            
            <a id="error-button" href="{{ route('wallet.deposit') }}" class="w-full hidden flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Thử lại
            </a>
            
            <button id="check-button" type="button" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Kiểm tra lại
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestId = '{{ $cardDeposit->request_id }}';
        const checkStatusUrl = '{{ route('wallet.card.check', ['requestId' => $cardDeposit->request_id]) }}';
        const walletIndexUrl = '{{ route('wallet.index') }}';
        const walletDepositUrl = '{{ route('wallet.deposit') }}';
        
        const statusElement = document.getElementById('status');
        const statusMessageElement = document.getElementById('status-message');
        const actualAmountElement = document.getElementById('actual-amount');
        const loadingSpinnerElement = document.getElementById('loading-spinner');
        const successButtonElement = document.getElementById('success-button');
        const errorButtonElement = document.getElementById('error-button');
        const checkButtonElement = document.getElementById('check-button');
        
        // Hàm kiểm tra trạng thái thẻ
        function checkStatus() {
            fetch(checkStatusUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Thẻ đã được xử lý thành công
                        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Thành công</span>';
                        statusMessageElement.className = 'bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6';
                        statusMessageElement.innerHTML = '<p class="font-medium">Nạp thẻ thành công!</p><p>Số tiền đã được cộng vào ví của bạn.</p>';
                        actualAmountElement.textContent = Intl.NumberFormat('vi-VN').format(data.actual_amount) + ' VNĐ';
                        
                        // Ẩn loading và hiển thị nút thành công
                        loadingSpinnerElement.classList.add('hidden');
                        successButtonElement.classList.remove('hidden');
                        checkButtonElement.classList.add('hidden');
                        
                        // Tự động chuyển về trang ví sau 5 giây
                        setTimeout(() => {
                            window.location.href = walletIndexUrl;
                        }, 5000);
                    } else if (data.status === 'failed') {
                        // Thẻ đã bị từ chối
                        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Thất bại</span>';
                        statusMessageElement.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6';
                        statusMessageElement.innerHTML = '<p class="font-medium">Nạp thẻ thất bại!</p><p>' + data.message + '</p>';
                        
                        // Ẩn loading và hiển thị nút thất bại
                        loadingSpinnerElement.classList.add('hidden');
                        errorButtonElement.classList.remove('hidden');
                        checkButtonElement.classList.add('hidden');
                    } else {
                        // Thẻ vẫn đang chờ xử lý
                        setTimeout(checkStatus, 5000); // Kiểm tra lại sau 5 giây
                    }
                })
                .catch(error => {
                    console.error('Error checking card status:', error);
                    // Nếu có lỗi, thử lại sau 10 giây
                    setTimeout(checkStatus, 10000);
                });
        }
        
        // Kiểm tra trạng thái ngay khi trang được tải
        checkStatus();
        
        // Xử lý sự kiện khi nhấn nút kiểm tra lại
        checkButtonElement.addEventListener('click', function() {
            checkStatus();
        });
    });
</script>
@endpush 