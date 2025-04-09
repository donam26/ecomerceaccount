@extends('admin.layouts.app')

@section('title', 'Điều chỉnh số dư ví - ' . $wallet->user->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Điều chỉnh số dư ví</h1>
        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin ví</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">ID ví:</label>
                        <input type="text" class="form-control" value="{{ $wallet->id }}" disabled>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Người dùng:</label>
                        <input type="text" class="form-control" value="{{ $wallet->user->name }} ({{ $wallet->user->email }})" disabled>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Số dư hiện tại:</label>
                        <input type="text" class="form-control" value="{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ" disabled>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Trạng thái:</label>
                        <input type="text" class="form-control" value="{{ $wallet->is_active ? 'Hoạt động' : 'Bị khóa' }}" disabled>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Điều chỉnh số dư</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.wallets.adjust.submit', $wallet->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="type" class="font-weight-bold">Loại điều chỉnh:</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="add">Nạp tiền (Thêm)</option>
                                <option value="subtract">Trừ tiền</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount" class="font-weight-bold">Số tiền:</label>
                            <div class="input-group">
                                <input type="number" name="amount" id="amount" class="form-control" placeholder="Nhập số tiền" min="1000" step="1000" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Nhập số tiền cần thêm hoặc trừ (tối thiểu 1.000 VNĐ)</small>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Mô tả:</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Nhập mô tả về việc điều chỉnh số dư" required></textarea>
                            <small class="form-text text-muted">Mô tả sẽ được hiển thị trong lịch sử giao dịch</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Lưu ý: Hành động này sẽ được ghi lại và không thể hoàn tác (ngoại trừ tạo giao dịch mới). Vui lòng kiểm tra kỹ thông tin trước khi xác nhận.
                        </div>

                        <button type="submit" class="btn btn-primary" onclick="return confirm('Bạn có chắc chắn muốn điều chỉnh số dư ví này?')">
                            <i class="fas fa-save"></i> Xác nhận điều chỉnh
                        </button>
                        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Format số tiền khi nhập
        $('#amount').on('input', function() {
            var val = $(this).val();
            if (val !== '') {
                val = parseInt(val);
                if (isNaN(val)) {
                    val = 0;
                }
                if (val < 1000) {
                    val = 1000;
                }
                $(this).val(val);
            }
        });
    });
</script>
@endsection 