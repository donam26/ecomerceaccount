@extends('layouts.app')

@section('title', 'Thanh toán thành công')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h1 class="mb-4">Thanh toán thành công!</h1>
                    
                    <div class="alert alert-success">
                        <p class="mb-0">Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đã được xác nhận.</p>
                    </div>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Thông tin đơn hàng</h5>
                            
                            <div class="row mb-2">
                                <div class="col-6 text-start text-muted">Mã đơn hàng:</div>
                                <div class="col-6 text-end fw-bold">{{ $order->order_number }}</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6 text-start text-muted">Trạng thái:</div>
                                <div class="col-6 text-end">
                                    <span class="badge bg-success">
                                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                                            @if($order->status == 'paid')
                                                Đã thanh toán
                                            @elseif($order->status == 'processing')
                                                Đang xử lý
                                            @elseif($order->status == 'completed')
                                                Hoàn thành
                                            @else
                                                {{ ucfirst($order->status) }}
                                            @endif
                                        @else
                                            Hoàn thành
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6 text-start text-muted">Số tiền:</div>
                                <div class="col-6 text-end fw-bold">{{ number_format($order->amount, 0, ',', '.') }}đ</div>
                            </div>
                            
                            @if(isset($isBoostingOrder) && $isBoostingOrder)
                                <div class="row mb-2">
                                    <div class="col-6 text-start text-muted">Dịch vụ:</div>
                                    <div class="col-6 text-end">{{ $order->service->name }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        @if(isset($isBoostingOrder) && $isBoostingOrder)
                            @if(!$order->hasAccountInfo())
                                <a href="{{ route('boosting.account_info', $order->order_number) }}" class="btn btn-primary btn-lg">
                                    Cung cấp thông tin tài khoản game
                                </a>
                            @else
                                <a href="{{ route('boosting.my_orders') }}" class="btn btn-primary btn-lg">
                                    Xem đơn hàng cày thuê của tôi
                                </a>
                            @endif
                        @else
                            <a href="{{ route('orders.show', $order->order_number) }}" class="btn btn-primary btn-lg">
                                Xem chi tiết đơn hàng
                            </a>
                        @endif
                        
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            Quay lại trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 