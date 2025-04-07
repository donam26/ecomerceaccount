@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #' . $order->order_number)

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
                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                        <a href="{{ route('boosting.index') }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Dịch vụ cày thuê</a>
                        @else
                        <a href="{{ route('orders.index') }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Đơn hàng của tôi</a>
                        @endif
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                        <span class="ml-1 text-gray-700 md:ml-2">Đơn hàng #{{ $order->order_number }}</span>
                        @else
                        <a href="{{ route('orders.show', $order->order_number) }}" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Đơn hàng #{{ $order->order_number }}</a>
                        @endif
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

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Chuyển khoản ngân hàng</h3>
                            
                            <div class="border border-blue-200 rounded-lg p-6 bg-blue-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Thông tin tài khoản ngân hàng</h4>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-1">Ngân hàng: <span class="font-medium text-gray-900">Vietcombank</span></p>
                                            <p class="text-sm text-gray-600 mb-1">Số tài khoản: <span class="font-medium text-gray-900">103870429701</span></p>
                                            <p class="text-sm text-gray-600 mb-1">Chủ tài khoản: <span class="font-medium text-gray-900">Do Hoang Nam</span></p>
                                            <p class="text-sm text-gray-600 mb-1">Chi nhánh: <span class="font-medium text-gray-900">Hồ Chí Minh</span></p>
                                        </div>
                                        <button type="button" id="copyBankInfo" class="inline-flex items-center px-3 py-1 border border-blue-600 text-blue-600 bg-white rounded-md hover:bg-blue-50 transition">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                                <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                            </svg>
                                            Sao chép
                                        </button>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-gray-800 mb-2">Thông tin chuyển khoản</h4>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-1">Số tiền: <span class="font-medium text-red-600">{{ isset($paymentInfo) ? number_format($paymentInfo['amount'], 0, ',', '.') : number_format($order->amount, 0, ',', '.') }}đ</span></p>
                                            <p class="text-sm text-gray-600 mb-1">Nội dung chuyển khoản:</p>
                                            <div class="flex items-center bg-white border border-gray-300 rounded-md p-2 mb-2">
                                                <span class="font-medium text-gray-900 select-all" id="paymentContent">{{ isset($paymentInfo) ? $paymentInfo['payment_content'] : 'SEVQR ORD'.$order->order_number }}</span>
                                                <button type="button" id="copyContent" class="ml-2 text-blue-600 hover:text-blue-800">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-red-600 font-medium">* Vui lòng nhập chính xác nội dung chuyển khoản</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-t border-blue-200 mt-4 pt-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h5 class="text-sm font-semibold text-gray-900">Lưu ý khi chuyển khoản</h5>
                                            <ul class="mt-1 text-sm text-gray-600 list-disc space-y-1 pl-5">
                                                <li>Bạn có thể chuyển khoản qua Internet Banking, Mobile Banking hoặc tại quầy.</li>
                                                <li>Hệ thống sẽ tự động ghi nhận và kích hoạt tài khoản sau khi nhận được tiền.</li>
                                                <li>Vui lòng giữ lại biên lai chuyển khoản để đối chiếu nếu cần.</li>
                                                <li>Sau khi chuyển khoản, vui lòng quay lại <a href="{{ route('orders.show', $order->order_number) }}" class="text-blue-600 hover:underline">trang đơn hàng</a> để kiểm tra trạng thái.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- QR Code thanh toán -->
                        <div class="mt-8 text-center">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quét mã QR để thanh toán nhanh</h3>
                            @if(isset($paymentInfo['qr_image']))
                                <!-- Hiển thị QR code từ SePay -->
                                <div class="bg-white p-4 inline-block rounded-lg border border-gray-300 mb-2">
                                    <img src="{{ $paymentInfo['qr_image'] }}" alt="QR Code thanh toán" class="mx-auto w-48 h-48">
                                    <p class="text-xs text-gray-500 mt-1">(QR từ SePay)</p>
                                </div>
                            @else
                                <!-- Sử dụng QR mặc định nếu không có từ API -->
                                @php
                                    $pattern = config('payment.pattern', 'SEVQR');
                                    // Kiểm tra mã đơn hàng đã có prefix "ORD" chưa
                                    if (strpos($order->order_number, 'ORD') === 0) {
                                        // Nếu đã có ORD thì không thêm vào nữa
                                        $content = $pattern . ' ' . $order->order_number;
                                    } else {
                                        // Nếu chưa có thì thêm vào
                                        $content = $pattern . ' ORD' . $order->order_number;
                                    }
                                    $amount = $order->amount;
                                    $encodedContent = urlencode($content);
                                    $qrUrl = "https://qr.sepay.vn/img?acc=103870429701&bank=VietinBank&amount={$amount}&des={$encodedContent}&template=compact";
                                @endphp
                                <div class="bg-white p-4 inline-block rounded-lg border border-gray-300 mb-2">
                                    <img src="{{ $qrUrl }}" alt="QR Code thanh toán" class="mx-auto w-48 h-48">
                                    <p class="text-xs text-gray-500 mt-1">(QR từ SePay)</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-8">
                            @if(isset($isBoostingOrder) && $isBoostingOrder)
                            <a href="{{ route('boosting.show', $order->service->slug) }}" class="btn-secondary">
                            @else
                            <a href="{{ route('orders.show', $order->order_number) }}" class="btn-secondary">
                            @endif
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    Quay lại thông tin đơn hàng
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột tóm tắt đơn hàng -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800">Tóm tắt đơn hàng</h2>
                    </div>
                    
                    <div class="p-6">
                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                        <!-- Hiển thị thông tin đơn hàng cày thuê -->
                        <div class="mb-4">
                            <h3 class="font-medium text-gray-900">{{ $order->service->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $order->service->game->name }}</p>
                            <p class="text-sm text-gray-500 mt-2">Thời gian ước tính: {{ $order->service->estimated_days }} ngày</p>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Giá dịch vụ</span>
                                <span class="font-medium text-gray-900">{{ number_format($order->original_amount, 0, ',', '.') }}đ</span>
                            </div>
                            
                            @if($order->discount > 0)
                                <div class="flex justify-between mb-2 text-green-600">
                                    <span>Giảm giá</span>
                                    <span>-{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between font-bold text-lg pt-4 border-t border-gray-200 mt-4">
                                <span>Tổng cộng</span>
                                <span class="text-red-600">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        @else
                        <!-- Hiển thị thông tin đơn hàng tài khoản thường -->
                        <div class="flex items-center mb-4">
                            @php
                                $accountImage = 'https://via.placeholder.com/300x200';
                                if ($order->account->images) {
                                    if (is_string($order->account->images)) {
                                        $images = json_decode($order->account->images, true);
                                        if (is_array($images) && !empty($images)) {
                                            $accountImage = asset('storage/' . $images[0]);
                                        }
                                    } elseif (is_array($order->account->images) && !empty($order->account->images)) {
                                        $accountImage = asset('storage/' . $order->account->images[0]);
                                    }
                                }
                            @endphp
                            <img src="{{ $accountImage }}" alt="{{ $order->account->title }}" class="w-16 h-16 object-cover rounded-md">
                            <div class="ml-4">
                                <h3 class="font-medium text-gray-900">{{ $order->account->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $order->account->game->name }}</p>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Giá tài khoản</span>
                                <span class="font-medium text-gray-900">{{ number_format($order->original_amount ?? $order->amount, 0, ',', '.') }}đ</span>
                            </div>
                            
                            @if(isset($order->discount) && $order->discount > 0)
                                <div class="flex justify-between mb-2 text-green-600">
                                    <span>Giảm giá</span>
                                    <span>-{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between font-bold text-lg pt-4 border-t border-gray-200 mt-4">
                                <span>Tổng cộng</span>
                                <span class="text-red-600">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Lấy các button
    const copyBankInfoBtn = document.getElementById('copyBankInfo');
    const copyContentBtn = document.getElementById('copyContent');
    
    // Xử lý sao chép thông tin ngân hàng
    copyBankInfoBtn.addEventListener('click', function() {
        const bankInfo = "Ngân hàng: Vietcombank\nSố tài khoản: 103870429701\nChủ tài khoản: CONG TY TNHH GAME SHOP\nChi nhánh: Hồ Chí Minh";
        navigator.clipboard.writeText(bankInfo).then(() => {
            copyBankInfoBtn.innerHTML = '<svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Đã sao chép';
            setTimeout(() => {
                copyBankInfoBtn.innerHTML = '<svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path><path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path></svg> Sao chép';
            }, 2000);
        });
    });
    
    // Xử lý sao chép nội dung chuyển khoản
    copyContentBtn.addEventListener('click', function() {
        const content = document.getElementById('paymentContent').innerText;
        navigator.clipboard.writeText(content).then(() => {
            copyContentBtn.innerHTML = '<svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
            setTimeout(() => {
                copyContentBtn.innerHTML = '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path><path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path></svg>';
            }, 2000);
        });
    });

    // Kiểm tra trạng thái đơn hàng
    const orderNumber = "{{ $order->order_number }}";
    const statusCheckUrl = "/orders/{{ $order->order_number }}/check-status";
    const successUrl = "/payment/success/{{ $order->order_number }}";
    const statusCheckElement = document.getElementById('payment-status-check');
    
    // Kiểm tra trạng thái mỗi 5 giây
    let checkCount = 0;
    const maxChecks = 60; // Kiểm tra tối đa 5 phút (60 lần x 5 giây)
    
    function updateStatusMessage(message, isError = false) {
        if (statusCheckElement) {
            // Cập nhật nội dung thông báo
            const messageSpan = statusCheckElement.querySelector('span');
            if (messageSpan) {
                messageSpan.textContent = message;
            }
            
            // Cập nhật kiểu thông báo nếu có lỗi
            if (isError) {
                statusCheckElement.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                statusCheckElement.classList.add('bg-red-50', 'border-red-500', 'text-red-700');
            }
        }
    }

    // Kiểm tra trạng thái đơn hàng mà không cần CSRF token
    function checkOrderStatus() {
        // Sử dụng XMLHttpRequest cơ bản thay vì fetch để tương thích tốt hơn
        const xhr = new XMLHttpRequest();
        const url = statusCheckUrl + "?t=" + new Date().getTime(); // Thêm timestamp để tránh cache
        
        xhr.open("GET", url, true);
        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                checkCount++;
                
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('Status check response:', data);
                        
                        if (data.success) {
                            // Kiểm tra xem có phải đơn hàng cày thuê không thông qua mã đơn hàng
                            const isBoostingOrder = orderNumber.indexOf('BOOST') === 0;
                            console.log('Is boosting order:', isBoostingOrder);
                            
                            // Kiểm tra các trạng thái thanh toán thành công
                            if (data.paid || 
                                data.status === 'completed' || 
                                data.status === 'paid' || 
                                data.status === 'processing') {
                                
                                console.log('Payment detected as successful! Status:', data.status);
                                updateStatusMessage('Thanh toán đã được xác nhận! Đang chuyển hướng...');
                                
                                // Ghi log xác nhận
                                console.log('Redirecting to success page:', successUrl);
                                
                                // Chuyển hướng đến trang thành công
                                setTimeout(function() {
                                    window.location.href = successUrl;
                                }, 1500);
                            } else {
                                // Cập nhật thông báo với số lần đã kiểm tra
                                updateStatusMessage(`Đang chờ xác nhận thanh toán... (${checkCount}/${maxChecks})`);
                                
                                // Tiếp tục kiểm tra nếu chưa đạt số lần tối đa
                                if (checkCount < maxChecks) {
                                    setTimeout(checkOrderStatus, 5000);
                                } else {
                                    updateStatusMessage('Đã hết thời gian chờ xác nhận tự động. Vui lòng làm mới trang nếu bạn đã thanh toán.', true);
                                }
                            }
                        } else {
                            updateStatusMessage('Không thể kiểm tra trạng thái đơn hàng: ' + (data.message || 'Lỗi không xác định'), true);
                        }
                    } catch (e) {
                        console.error('Lỗi phân tích JSON:', e);
                        updateStatusMessage('Lỗi xử lý phản hồi từ máy chủ', true);
                    }
                } else {
                    console.error('Lỗi HTTP:', xhr.status);
                    updateStatusMessage('Lỗi kết nối đến máy chủ (HTTP ' + xhr.status + ')', true);
                    
                    // Tiếp tục kiểm tra nếu có lỗi và chưa đạt giới hạn
                    if (checkCount < maxChecks) {
                        setTimeout(checkOrderStatus, 5000);
                    } else {
                        updateStatusMessage('Đã hết thời gian chờ xác nhận tự động. Vui lòng làm mới trang.', true);
                    }
                }
            }
        };
        
        xhr.timeout = 10000; // 10 giây timeout
        xhr.ontimeout = function() {
            console.error('Yêu cầu kiểm tra trạng thái đã hết thời gian chờ');
            updateStatusMessage('Yêu cầu kiểm tra đã hết thời gian chờ. Đang thử lại...', true);
            
            if (checkCount < maxChecks) {
                setTimeout(checkOrderStatus, 5000);
            } else {
                updateStatusMessage('Đã hết thời gian chờ xác nhận tự động. Vui lòng làm mới trang.', true);
            }
        };
        
        try {
            xhr.send();
        } catch (e) {
            console.error('Lỗi gửi yêu cầu:', e);
            updateStatusMessage('Không thể gửi yêu cầu kiểm tra đến máy chủ', true);
        }
    }

    // Bắt đầu kiểm tra sau khi trang đã tải
    document.addEventListener('DOMContentLoaded', function() {
        // Đợi 5 giây trước khi bắt đầu kiểm tra đầu tiên
        setTimeout(checkOrderStatus, 5000);
    });

    // Hàm kiểm tra thủ công
    function manualCheckStatus() {
        // Hiển thị thông báo đang kiểm tra
        updateStatusMessage('Đang kiểm tra thủ công...');
        
        // Thực hiện kiểm tra
        const xhr = new XMLHttpRequest();
        const url = statusCheckUrl + "?manual=1&t=" + new Date().getTime();
        
        xhr.open("GET", url, true);
        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('Manual status check response:', data);
                        
                        if (data.success) {
                            // Kiểm tra xem có phải đơn hàng cày thuê không thông qua mã đơn hàng
                            const isBoostingOrder = orderNumber.indexOf('BOOST') === 0;
                            console.log('Is boosting order (manual check):', isBoostingOrder);
                            
                            // Kiểm tra các trạng thái thanh toán thành công
                            if (data.paid || 
                                data.status === 'completed' || 
                                data.status === 'paid' || 
                                data.status === 'processing') {
                                
                                console.log('Manual check: Payment detected as successful! Status:', data.status);
                                updateStatusMessage('Thanh toán đã được xác nhận! Đang chuyển hướng...');
                                
                                // Chuyển hướng đến trang thành công
                                setTimeout(function() {
                                    window.location.href = successUrl;
                                }, 1500);
                            } else {
                                updateStatusMessage('Chưa nhận được xác nhận thanh toán. Vui lòng đợi thêm hoặc kiểm tra lại sau.', false);
                            }
                        } else {
                            updateStatusMessage('Không thể kiểm tra trạng thái đơn hàng: ' + (data.message || 'Lỗi không xác định'), true);
                        }
                    } catch (e) {
                        console.error('Lỗi phân tích JSON:', e);
                        updateStatusMessage('Lỗi xử lý phản hồi từ máy chủ', true);
                    }
                } else {
                    console.error('Lỗi HTTP:', xhr.status);
                    updateStatusMessage('Lỗi kết nối đến máy chủ (HTTP ' + xhr.status + ')', true);
                }
            }
        };
        
        xhr.timeout = 10000;
        xhr.ontimeout = function() {
            console.error('Yêu cầu kiểm tra thủ công đã hết thời gian chờ');
            updateStatusMessage('Yêu cầu kiểm tra thủ công đã hết thời gian chờ', true);
        };
        
        try {
            xhr.send();
        } catch (e) {
            console.error('Lỗi gửi yêu cầu thủ công:', e);
            updateStatusMessage('Không thể gửi yêu cầu kiểm tra đến máy chủ', true);
        }
    }
</script>
@endsection 