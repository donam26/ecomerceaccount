@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa ví điện tử - ' . $wallet->user->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa ví điện tử</h1>
        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

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
                    <form action="{{ route('admin.wallets.update', $wallet->id) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                            <small class="form-text text-muted">Để điều chỉnh số dư, vui lòng sử dụng chức năng "Điều chỉnh số dư".</small>
                        </div>

                        <div class="form-group">
                            <label for="is_active" class="font-weight-bold">Trạng thái:</label>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="1" {{ $wallet->is_active ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ !$wallet->is_active ? 'selected' : '' }}>Khóa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Ngày tạo:</label>
                            <input type="text" class="form-control" value="{{ $wallet->created_at->format('d/m/Y H:i') }}" disabled>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Cập nhật cuối:</label>
                            <input type="text" class="form-control" value="{{ $wallet->updated_at->format('d/m/Y H:i') }}" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Thao tác khác</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Điều chỉnh số dư</h5>
                        <p>Sử dụng chức năng này để nạp tiền hoặc trừ tiền trong ví của người dùng.</p>
                        <a href="{{ route('admin.wallets.adjust', $wallet->id) }}" class="btn btn-warning">
                            <i class="fas fa-money-bill"></i> Điều chỉnh số dư
                        </a>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Xem lịch sử giao dịch</h5>
                        <p>Xem chi tiết lịch sử giao dịch của ví người dùng này.</p>
                        <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> Xem lịch sử giao dịch
                        </a>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Quản lý người dùng</h5>
                        <p>Chỉnh sửa thông tin người dùng sở hữu ví này.</p>
                        <a href="{{ route('admin.users.edit', $wallet->user->id) }}" class="btn btn-primary">
                            <i class="fas fa-user-edit"></i> Quản lý người dùng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 