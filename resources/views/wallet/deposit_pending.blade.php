@extends('layouts.app')

@section('title', 'Đang chờ xác nhận thanh toán')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Đang chờ xác nhận thanh toán</h2>
            
            <div class="text-center mb-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Đang xử lý...</span>
                </div>
                <p class="fs-5">Hệ thống đang xác nhận giao dịch của bạn. Vui lòng đợi trong giây lát...</p>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin giao dịch</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Số tiền:</strong> {{ number_format($deposit['amount']) }} VNĐ</p>
                            <p><strong>Mã giao dịch:</strong> {{ $deposit['code'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Thời gian gửi:</strong> {{ $deposit['created_at'] }}</p>
                            <p><strong>Trạng thái:</strong> <span id="payment-status" class="badge bg-warning">Đang xử lý</span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="result-container" class="d-none">
                <div class="alert alert-success d-none" id="success-message">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    <span id="success-text">Thanh toán thành công! Số dư đã được cập nhật vào ví của bạn.</span>
                </div>
                
                <div class="alert alert-danger d-none" id="error-message">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="error-text">Thanh toán thất bại. Vui lòng thử lại hoặc liên hệ bộ phận hỗ trợ.</span>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('wallet.deposit') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </a>
                <a href="{{ route('wallet.index') }}" class="btn btn-primary">
                    <i class="bi bi-wallet2 me-2"></i>Xem ví của tôi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tự động chuyển về trang ví sau 5 giây
        setTimeout(function() {
            window.location.href = "{{ route('wallet.index') }}";
        }, 5000);
    });
</script>
@endpush 