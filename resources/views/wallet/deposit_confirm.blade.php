@extends('layouts.app')

@section('title', 'Xác nhận nạp tiền')

@section('content')
<div class="container py-4">
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Quét mã QR để nạp tiền</h2>
            
            <div class="flex justify-center mb-6">
                <div class="text-center">
                    <img src="{{ $paymentInfo['qr_url'] }}" alt="QR Code" class="mx-auto mb-3 max-w-[300px]">
                    <h4 class="text-lg font-medium text-gray-900">Số tiền: {{ number_format($paymentInfo['amount']) }} VNĐ</h4>
                </div>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" id="status-container">
                <h5 class="font-bold">Hướng dẫn thanh toán:</h5>
                <ol class="mt-2 ml-4 space-y-1">
                    <li>1. Mở ứng dụng ngân hàng trên điện thoại của bạn</li>
                    <li>2. Chọn chức năng quét mã QR</li>
                    <li>3. Quét mã QR hiển thị ở trên</li>
                    <li>4. Xác nhận giao dịch với nội dung: <strong>{{ $paymentInfo['payment_content'] }}</strong></li>
                    <li>5. Sau khi thanh toán thành công, số dư sẽ được cập nhật trong vòng 1-2 phút</li>
                </ol>
                <div class="mt-4 flex items-center" id="checking-status">
                    <svg class="animate-spin h-5 w-5 mr-2 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Đang kiểm tra trạng thái thanh toán...</span>
                </div>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="font-medium text-gray-700">Thông tin giao dịch</h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><span class="font-medium text-gray-700">Số tiền:</span> {{ number_format($paymentInfo['amount']) }} VNĐ</p>
                            <p class="mb-2"><span class="font-medium text-gray-700">Mã giao dịch:</span> {{ $paymentInfo['deposit_code'] }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><span class="font-medium text-gray-700">Nội dung chuyển khoản:</span> 
                                <span id="payment-content" class="select-all">{{ $paymentInfo['payment_content'] }}</span>
                                <button type="button" class="copy-btn ml-2 text-blue-600 hover:text-blue-800" data-clipboard-target="#payment-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                            </p>
                            <p><span class="font-medium text-gray-700">Trạng thái:</span> 
                                <span id="payment-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Đang chờ thanh toán
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <a href="{{ route('wallet.deposit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Quay lại
                </a>
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Về trang ví
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo Clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            // Hiển thị thông báo sao chép thành công
            const originalHTML = e.trigger.innerHTML;
            e.trigger.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
            
            // Hiển thị thông báo
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            
            Toast.fire({
                icon: 'success',
                title: 'Đã sao chép nội dung'
            });
            
            setTimeout(function() {
                e.trigger.innerHTML = originalHTML;
            }, 2000);
            
            e.clearSelection();
        });
        
        // Lấy thông tin từ trang
        const depositCode = "{{ $paymentInfo['deposit_code'] }}";
        const amount = "{{ $paymentInfo['amount'] }}";
        
        // Hàm kiểm tra trạng thái thanh toán
        function checkPaymentStatus() {
            // Gửi request kiểm tra trạng thái thanh toán
            $.ajax({
                url: "{{ route('wallet.deposit.check') }}",
                type: "GET",
                data: {
                    deposit_code: depositCode,
                    amount: amount
                },
                dataType: "json",
                success: function(response) {
                    console.log('Kiểm tra trạng thái thanh toán:', response);
                    
                    if (response.success && (response.status === 'completed' || response.status === 'success')) {
                        // Cập nhật UI trạng thái
                        updateStatusUI('success', response.message || 'Thanh toán thành công!');
                        
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Thanh toán thành công!',
                            text: 'Số dư của bạn đã được cập nhật',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Chuyển hướng sau 2 giây
                        setTimeout(function() {
                            window.location.href = "{{ route('wallet.index') }}";
                        }, 2000);
                        
                        // Dừng kiểm tra
                        clearInterval(checkInterval);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi khi kiểm tra trạng thái:', error);
                }
            });
        }
        
        // Cập nhật UI trạng thái thanh toán
        function updateStatusUI(status, message) {
            // Ẩn phần đang kiểm tra
            document.getElementById('checking-status').classList.add('hidden');
            
            // Cập nhật trạng thái
            const paymentStatus = document.getElementById('payment-status');
            const statusContainer = document.getElementById('status-container');
            
            if (status === 'success') {
                paymentStatus.classList.remove('bg-yellow-100', 'text-yellow-800');
                paymentStatus.classList.add('bg-green-100', 'text-green-800');
                paymentStatus.textContent = 'Thanh toán thành công';
                
                statusContainer.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                statusContainer.classList.add('bg-green-50', 'border-green-500', 'text-green-700');
                
                // Thêm thông báo thành công
                statusContainer.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-medium">${message}</span>
                    </div>
                    <p class="mt-2">Bạn sẽ được chuyển hướng đến trang ví trong giây lát...</p>
                `;
            }
        }
        
        // Kiểm tra ngay lập tức và sau đó mỗi 10 giây
        checkPaymentStatus();
        const checkInterval = setInterval(checkPaymentStatus, 10000);
    });
</script>
@endpush 