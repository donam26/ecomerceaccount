@extends('admin.layouts.app')

@section('title', 'Quản lý ví điện tử')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý ví điện tử</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách ví người dùng</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Tùy chọn:</div>
                    <a class="dropdown-item" href="{{ route('admin.transactions.index') }}">Lịch sử giao dịch</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8">
                    <form action="{{ route('admin.wallets.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-2">
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên/email" value="{{ request('search') }}">
                        </div>
                        <div class="form-group mr-2">
                            <select name="status" class="form-control">
                                <option value="">-- Trạng thái --</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Bị khóa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search fa-sm"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <form action="{{ route('admin.wallets.index') }}" method="GET" class="form-inline justify-content-end">
                        <div class="form-group">
                            <select name="sort" class="form-control form-control-sm mr-2">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                                <option value="balance" {{ request('sort') == 'balance' ? 'selected' : '' }}>Số dư</option>
                                <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Cập nhật cuối</option>
                            </select>
                            <select name="order" class="form-control form-control-sm">
                                <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary ml-2">
                            <i class="fas fa-sort fa-sm"></i> Sắp xếp
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Số dư</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Cập nhật cuối</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wallets as $wallet)
                        <tr>
                            <td>{{ $wallet->id }}</td>
                            <td>
                                <div>{{ $wallet->user->name }}</div>
                                <small class="text-muted">{{ $wallet->user->email }}</small>
                            </td>
                            <td>{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ</td>
                            <td>
                                @if($wallet->is_active)
                                <span class="badge badge-success">Hoạt động</span>
                                @else
                                <span class="badge badge-danger">Bị khóa</span>
                                @endif
                            </td>
                            <td>{{ $wallet->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $wallet->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.wallets.show', $wallet->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('admin.wallets.adjust', $wallet->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-money-bill"></i> Điều chỉnh
                                    </a>
                                    <a href="{{ route('admin.wallets.edit', $wallet->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $wallets->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection 