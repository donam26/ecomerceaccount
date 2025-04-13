@extends('layouts.app')

@section('title', 'Thanh toán thành công')

@section('content')
<div class="container mx-auto px-4 py-8 sm:py-12">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Banner bên trái -->
            <div class="hidden lg:block lg:w-5/12 bg-gradient-to-br from-blue-500 to-blue-800 text-white">
                <div class="h-full flex flex-col justify-center p-8">
                    <div class="mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-3">Thanh toán thành công!</h2>
                    <p class="text-lg mb-6">Cảm ơn bạn đã mua sắm tại ShopBuffsao. Chúng tôi đã xác nhận đơn hàng của bạn.</p>
                    <div class="mb-3 space-y-3">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Thanh toán an toàn & bảo mật</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Hỗ trợ khách hàng 24/7</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Cam kết hoàn tiền nếu có vấn đề</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin bên phải -->
            <div class="w-full lg:w-7/12">
                <div class="p-6 lg:p-8">
                    <div class="text-center mb-8">
                        <div class="success-checkmark mb-6">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                                <div class="icon-circle"></div>
                                <div class="icon-fix"></div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Đơn hàng của bạn đã được xác nhận!</h3>
                        <p class="text-gray-600">Một email xác nhận sẽ được gửi đến địa chỉ email của bạn</p>
                    </div>
                    
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <div class="bg-gray-50 px-4 py-3 rounded-t-xl border-b">
                            <h5 class="font-bold text-gray-800">Thông tin đơn hàng</h5>
                        </div>
                        <div class="p-4 space-y-3 divide-y divide-gray-100">
                            <div class="flex justify-between items-center py-2">
                                <div class="text-gray-500">Mã đơn hàng:</div>
                                <div class="font-bold">{{ $order->order_number }}</div>
                            </div>
                            
                            <div class="flex justify-between items-center py-2">
                                <div class="text-gray-500">Trạng thái:</div>
                                <div>
                                    @if(isset($isBoostingOrder) && $isBoostingOrder)
                                        @if($order->status == 'paid')
                                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Đã thanh toán</span>
                                        @elseif($order->status == 'processing')
                                            <span class="bg-blue-400 text-white text-xs px-2 py-1 rounded-full">Đang xử lý</span>
                                        @elseif($order->status == 'completed')
                                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Hoàn thành</span>
                                        @else
                                            <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded-full">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    @else
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Hoàn thành</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center py-2">
                                <div class="text-gray-500">Ngày tạo:</div>
                                <div>{{ $order->created_at->format('H:i d/m/Y') }}</div>
                            </div>
                            
                            <div class="flex justify-between items-center py-2">
                                <div class="text-gray-500">Số tiền:</div>
                                <div class="font-bold text-green-600">{{ number_format($order->amount, 0, ',', '.') }}đ</div>
                            </div>
                            
                            @if(isset($isBoostingOrder) && $isBoostingOrder)
                                <div class="flex justify-between items-center py-2">
                                    <div class="text-gray-500">Dịch vụ:</div>
                                    <div>{{ $order->service->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid gap-4">
                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                            @if(!$order->hasAccountInfo())
                                <a href="{{ route('boosting.account_info', $order->order_number) }}" class="flex justify-center items-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Cung cấp thông tin tài khoản game
                                </a>
                            @else
                                <a href="{{ route('boosting.my_orders') }}" class="flex justify-center items-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                    </svg>
                                    Xem đơn hàng dịch vụ của tôi
                                </a>
                            @endif
                        @else
                            @if(strpos($order->order_number, 'SRV-') === 0)
                                <a href="{{ route('services.view_order', $order->order_number) }}" class="flex justify-center items-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Xem chi tiết đơn hàng
                                </a>
                            @elseif(strpos($order->order_number, 'BST-') === 0)
                                <a href="{{ route('boosting.orders.show', $order->order_number) }}" class="flex justify-center items-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Xem chi tiết đơn hàng
                                </a>
                            @else
                                <a href="{{ route('orders.index') }}" class="flex justify-center items-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    Xem chi tiết đơn hàng
                                </a>
                            @endif
                        @endif
                        
                        <a href="{{ route('home') }}" class="flex justify-center items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Quay lại trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Success checkmark animation */
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
}

.success-checkmark .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #4CAF50;
}

.success-checkmark .check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.success-checkmark .check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}

.success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
    content: '';
    height: 100px;
    position: absolute;
    background: #FFFFFF;
    transform: rotate(-45deg);
}

.success-checkmark .check-icon .icon-line {
    height: 5px;
    background-color: #4CAF50;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .check-icon .icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}

.success-checkmark .check-icon .icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}

.success-checkmark .check-icon .icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(76, 175, 80, .5);
}

.success-checkmark .check-icon .icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: #FFFFFF;
}

@keyframes rotate-circle {
    0% { transform: rotate(-45deg); }
    5% { transform: rotate(-45deg); }
    12% { transform: rotate(-405deg); }
    100% { transform: rotate(-405deg); }
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 45px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}
</style>
@endsection 