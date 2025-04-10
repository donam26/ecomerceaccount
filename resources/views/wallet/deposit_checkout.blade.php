@extends('layouts.app')

@section('title', 'Thanh toán nạp tiền')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('wallet.index') }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Ví của tôi</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('wallet.deposit') }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Nạp tiền</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Thanh toán</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cột thông tin thanh toán -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800">Thông tin thanh toán</h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Hiển thị trạng thái thanh toán -->
                        <div id="payment-status-check" class="my-5 px-4 py-3 border-l-4 border-blue-500 bg-blue-50 text-blue-700">
                            <span>Đang kiểm tra trạng thái thanh toán...</span>
                            
                            <div class="mt-3 flex space-x-3">
                                <button onclick="manualCheckStatus()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none">
                                    Kiểm tra thủ công
                                </button>
                                <button onclick="window.location.reload()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none">
                                    Làm mới trang
                                </button>
                            </div>
                        </div>

                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Phương thức thanh toán</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Vui lòng chuyển khoản theo thông tin bên dưới</p>
                                </div>
                                <div>
                                    <button id="refreshStatusBtn" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Kiểm tra thanh toán
                                    </button>
                                </div>
                            </div>

                            <div class="border-t border-gray-200">
                                <div class="px-6 py-5 space-y-6">
                                    <!-- Chuyển khoản ngân hàng -->
                                    <div class="relative border rounded-md p-4">
                                        <h3 class="text-lg font-medium text-gray-900">Thanh toán chuyển khoản</h3>
                                        
                                        <div class="mt-4 grid md:grid-cols-2 gap-4">
                                            <div>
                                                <div class="mb-4">
                                                    <p class="text-sm font-medium text-gray-700">Ngân hàng:</p>
                                                    <p class="text-base font-semibold">MBBank</p>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-sm font-medium text-gray-700">Số tài khoản:</p>
                                                    <p class="text-base font-semibold">
                                                        0386702324
                                                        <button type="button" class="ml-2 inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 copy-btn" data-clipboard-text="0386702324">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                            </svg>
                                                        </button>
                                                    </p>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-sm font-medium text-gray-700">Chủ tài khoản:</p>
                                                    <p class="text-base font-semibold">NGUYEN THI BICH NGOC
                                                    </p>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-sm font-medium text-gray-700">Số tiền:</p>
                                                    <p class="text-base font-semibold">{{ number_format($depositOrder->amount, 0, ',', '.') }}đ
                                                        <button type="button" class="ml-2 inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 copy-btn" data-clipboard-text="{{ $depositOrder->amount }}">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                            </svg>
                                                        </button>
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">Nội dung chuyển khoản:</p>
                                                    <p class="text-base font-semibold">
                                                        {{ $paymentInfo['payment_content'] }}
                                                        <button type="button" class="ml-2 inline-flex items-center p-1 border border-transparent rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 copy-btn" data-clipboard-text="{{ $paymentInfo['payment_content'] }}">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                            </svg>
                                                        </button>
                                                    </p>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        (Lưu ý: Vui lòng sử dụng đúng nội dung chuyển khoản này để hệ thống xác nhận tự động)
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center">
                                                <img src="{{ $paymentInfo['qr_url'] }}" alt="QR Thanh toán" class="max-w-full h-auto">
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6">
                                            <p class="text-sm text-gray-500">
                                                <strong>Lưu ý:</strong> Hệ thống tự động xác nhận thanh toán sau 1-3 phút. Nếu quá thời gian trên mà chưa nhận được xác nhận, vui lòng liên hệ hotline để được hỗ trợ.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột tóm tắt thông tin nạp tiền -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800">Thông tin nạp tiền</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="font-medium text-gray-900">Nạp tiền vào ví</h3>
                            <p class="text-sm text-gray-500 mt-2">Mã nạp tiền: {{ $depositOrder->order_number }}</p>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Số dư hiện tại</span>
                                <span class="font-medium text-gray-900">{{ number_format($wallet->balance, 0, ',', '.') }}đ</span>
                            </div>
                            
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Số tiền nạp</span>
                                <span class="font-medium text-green-600">+{{ number_format($depositOrder->amount, 0, ',', '.') }}đ</span>
                            </div>
                            
                            <div class="flex justify-between font-bold text-lg pt-4 border-t border-gray-200 mt-4">
                                <span>Số dư sau nạp</span>
                                <span class="text-blue-600">{{ number_format($wallet->balance + $depositOrder->amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('wallet.deposit') }}" class="mt-6 w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Thay đổi số tiền nạp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo Clipboard.js
        var clipboard = new ClipboardJS('.copy-btn');
        
        clipboard.on('success', function(e) {
            // Hiển thị thông báo sao chép thành công
            const originalText = e.trigger.innerHTML;
            e.trigger.innerHTML = '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            
            setTimeout(function() {
                e.trigger.innerHTML = originalText;
            }, 2000);
            
            e.clearSelection();
        });
        
        // Kiểm tra trạng thái thanh toán tự động
        const depositCode = '{{ $depositOrder->order_number }}';
        const checkStatusUrl = '{{ route('payment.check_status', ['orderNumber' => $depositOrder->order_number]) }}';
        
        function checkPaymentStatus() {
            fetch(checkStatusUrl)
                .then(response => response.json())
                .then(data => {
                    
                    if (data.status === 'paid' || data.status === 'completed' || data.status === 'processing') {
                        // Hiển thị thông báo thành công
                        showSuccessMessage(data.message);
                        
                        // Chuyển hướng sau 2 giây
                        setTimeout(function() {
                            if (data.status === 'paid' || data.status === 'completed') {
                                window.location.href = '{{ route('wallet.index') }}';
                            }
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi kiểm tra trạng thái:', error);
                });
        }
        
        // Kiểm tra trạng thái mỗi 10 giây
        const statusInterval = setInterval(checkPaymentStatus, 10000);
        
        // Nút kiểm tra trạng thái thủ công
        document.getElementById('refreshStatusBtn').addEventListener('click', function() {
            // Thay đổi trạng thái nút
            this.classList.add('animate-pulse');
            this.disabled = true;
            
            fetch(checkStatusUrl)
                .then(response => response.json())
                .then(data => {
                    
                    // Khôi phục trạng thái nút
                    this.classList.remove('animate-pulse');
                    this.disabled = false;
                    
                    if (data.status === 'paid' || data.status === 'completed' || data.status === 'processing') {
                        // Hiển thị thông báo thành công
                        showSuccessMessage(data.message);
                        
                        // Chuyển hướng sau 2 giây
                        setTimeout(function() {
                            if (data.status === 'paid' || data.status === 'completed') {
                                window.location.href = '{{ route('wallet.index') }}';
                            }
                        }, 2000);
                    } else {
                        // Hiển thị thông báo chưa thanh toán
                        showErrorMessage('Hệ thống chưa ghi nhận thanh toán của bạn. Vui lòng kiểm tra lại sau.');
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi kiểm tra trạng thái:', error);
                    
                    // Khôi phục trạng thái nút
                    this.classList.remove('animate-pulse');
                    this.disabled = false;
                    
                    showErrorMessage('Đã xảy ra lỗi khi kiểm tra. Vui lòng thử lại sau.');
                });
        });
        
        function showSuccessMessage(message) {
            // Tạo phần tử thông báo
            var notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg transition-all duration-500 transform translate-x-full';
            notification.textContent = message || 'Đã xác nhận thanh toán thành công!';
            
            // Thêm vào body
            document.body.appendChild(notification);
            
            // Hiển thị
            setTimeout(function() {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Ẩn sau 5 giây
            setTimeout(function() {
                notification.classList.add('translate-x-full');
                setTimeout(function() {
                    document.body.removeChild(notification);
                }, 500);
            }, 5000);
        }
        
        function showErrorMessage(message) {
            // Tạo phần tử thông báo
            var notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg transition-all duration-500 transform translate-x-full';
            notification.textContent = message || 'Đã xảy ra lỗi!';
            
            // Thêm vào body
            document.body.appendChild(notification);
            
            // Hiển thị
            setTimeout(function() {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Ẩn sau 5 giây
            setTimeout(function() {
                notification.classList.add('translate-x-full');
                setTimeout(function() {
                    document.body.removeChild(notification);
                }, 500);
            }, 5000);
        }
        
        // Thêm chức năng kiểm tra thủ công
        window.manualCheckStatus = function() {
            document.getElementById('refreshStatusBtn').click();
        };
    });
</script>
@endsection 