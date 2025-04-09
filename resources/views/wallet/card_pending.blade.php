@extends('layouts.app')

@section('title', 'Đang xử lý thẻ cào')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Đang xử lý thẻ cào</h2>
            
            <div class="text-center mb-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Đang xử lý...</span>
                </div>
                <p class="fs-5">Hệ thống đang kiểm tra thông tin thẻ cào của bạn. Vui lòng đợi trong giây lát...</p>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin thẻ cào</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nhà mạng:</strong> {{ $cardDeposit->getTelcoNameAttribute() }}</p>
                            <p><strong>Mệnh giá:</strong> {{ number_format($cardDeposit->amount) }} VNĐ</p>
                            <p><strong>Serial:</strong> {{ substr($cardDeposit->serial, 0, 4) . '******' . substr($cardDeposit->serial, -4) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Mã thẻ:</strong> {{ substr($cardDeposit->code, 0, 4) . '******' . substr($cardDeposit->code, -4) }}</p>
                            <p><strong>Thời gian gửi:</strong> {{ $cardDeposit->created_at->format('H:i:s d/m/Y') }}</p>
                            <p><strong>Trạng thái:</strong> <span id="card-status" class="badge bg-warning">Đang xử lý</span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="result-container" class="d-none">
                <div class="alert alert-success d-none" id="success-message">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    <span id="success-text"></span>
                </div>
                
                <div class="alert alert-danger d-none" id="error-message">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="error-text"></span>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('wallet.deposit') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </a>
                <a href="{{ route('wallet.index') }}" class="btn btn-primary d-none" id="to-wallet-btn">
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
        function checkCardStatus() {
            $.ajax({
                url: '{{ route("wallet.card.check", $cardDeposit->request_id) }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'completed') {
                        // Hiển thị thông báo thành công
                        $('#card-status').removeClass('bg-warning').addClass('bg-success').text('Thành công');
                        $('#success-message').removeClass('d-none');
                        $('#success-text').text(response.message);
                        $('#result-container').removeClass('d-none');
                        $('#to-wallet-btn').removeClass('d-none');
                        
                        // Dừng kiểm tra
                        clearInterval(checkInterval);
                        
                        // Chuyển hướng sau 5 giây nếu có URL chuyển hướng
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 5000);
                        }
                    } else if (response.status === 'failed') {
                        // Hiển thị thông báo lỗi
                        $('#card-status').removeClass('bg-warning').addClass('bg-danger').text('Thất bại');
                        $('#error-message').removeClass('d-none');
                        $('#error-text').text(response.message);
                        $('#result-container').removeClass('d-none');
                        
                        // Dừng kiểm tra
                        clearInterval(checkInterval);
                        
                        // Chuyển hướng sau 5 giây nếu có URL chuyển hướng
                        if (response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 5000);
                        }
                    } else {
                        // Vẫn đang xử lý, không làm gì cả
                        console.log('Đang kiểm tra thẻ cào...');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi khi kiểm tra trạng thái thẻ cào:', error);
                }
            });
        }
        
        // Kiểm tra ngay lập tức và sau đó mỗi 5 giây
        checkCardStatus();
        let checkInterval = setInterval(checkCardStatus, 5000);
    });
</script>
@endpush 