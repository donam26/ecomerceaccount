@extends('layouts.app')

@section('title', 'Thanh toán qua VNPay')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-lg mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-center mb-4">
                    <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694424446384.png" alt="VNPay" class="h-12">
                </div>
                <h1 class="text-2xl font-bold text-center text-gray-900">Thanh toán qua VNPay</h1>
                <p class="mt-2 text-center text-gray-500">(Mô phỏng thanh toán)</p>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Thông tin thanh toán</h2>
                    
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Số tiền:</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($amount, 0, ',', '.') }} VNĐ</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Mã đơn hàng:</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $orderId }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Nội dung thanh toán:</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $orderType == 'deposit' ? 'Nạp tiền vào ví' : 'Thanh toán đơn hàng' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Chọn phương thức thanh toán</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-md p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50">
                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/bank/qr-icon.png" alt="QR Code" class="h-8 mb-2">
                            <p class="text-sm font-medium text-gray-900">QR Code</p>
                        </div>
                        <div class="border border-gray-200 rounded-md p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50">
                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/bank/atm-icon.png" alt="ATM" class="h-8 mb-2">
                            <p class="text-sm font-medium text-gray-900">Thẻ ATM</p>
                        </div>
                        <div class="border border-gray-200 rounded-md p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50">
                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/bank/credit-icon.png" alt="Credit" class="h-8 mb-2">
                            <p class="text-sm font-medium text-gray-900">Thẻ quốc tế</p>
                        </div>
                        <div class="border border-gray-200 rounded-md p-4 flex flex-col items-center cursor-pointer hover:bg-gray-50">
                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/bank/bank-transfer-icon.png" alt="Bank Transfer" class="h-8 mb-2">
                            <p class="text-sm font-medium text-gray-900">Chuyển khoản</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <form action="{{ route('payment.vnpay.result') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $orderId }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="order_type" value="{{ $orderType }}">
                        <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                        <input type="hidden" name="vnp_ResponseCode" value="00">
                        
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Thanh toán thành công
                        </button>
                    </form>
                    
                    <form action="{{ route('payment.vnpay.result') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $orderId }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="order_type" value="{{ $orderType }}">
                        <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                        <input type="hidden" name="vnp_ResponseCode" value="24">
                        
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Hủy thanh toán
                        </button>
                    </form>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">Lưu ý: Đây là môi trường giả lập thanh toán phục vụ mục đích thử nghiệm.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 