@extends('layouts.app')

@section('title', 'Nạp tiền vào ví')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Nạp tiền vào ví</h2>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Nạp tiền qua chuyển khoản ngân hàng</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('wallet.deposit.process') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Số tiền muốn nạp (VNĐ)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="10000" step="10000" value="100000" required>
                                    <div class="form-text">Tối thiểu 10,000 VNĐ</div>
                                </div>
                                
                                <input type="hidden" name="payment_method" value="bank_transfer">
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Tiếp tục</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Nạp tiền bằng thẻ cào</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('wallet.deposit.card') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="telco" class="form-label">Nhà mạng</label>
                                    <select class="form-select" id="telco" name="telco" required>
                                        <option value="VIETTEL">Viettel</option>
                                        <option value="MOBIFONE">Mobifone</option>
                                        <option value="VINAPHONE">Vinaphone</option>
                                        <option value="VIETNAMOBILE">Vietnamobile</option>
                                        <option value="ZING">Zing</option>
                                        <option value="GATE">Gate</option>
                                        <option value="VCOIN">VCoin</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Mệnh giá</label>
                                    <select class="form-select" id="amount" name="amount" required>
                                        <option value="10000">10,000 VNĐ</option>
                                        <option value="20000">20,000 VNĐ</option>
                                        <option value="50000">50,000 VNĐ</option>
                                        <option value="100000">100,000 VNĐ</option>
                                        <option value="200000">200,000 VNĐ</option>
                                        <option value="500000">500,000 VNĐ</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="serial" class="form-label">Số serial</label>
                                    <input type="text" class="form-control" id="serial" name="serial" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Mã thẻ</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Nạp thẻ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 