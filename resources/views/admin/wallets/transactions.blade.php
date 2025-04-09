@extends('admin.layouts.app')

@section('title', 'Quản lý giao dịch ví điện tử')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý giao dịch ví điện tử</h1>
        <a href="{{ route('admin.wallets.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-wallet"></i> Quản lý ví
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

    <!-- Thống kê -->
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê giao dịch</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Tổng nạp tiền
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_deposit'], 0, ',', '.') }} VNĐ</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Tổng thanh toán
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(abs($stats['total_payment']), 0, ',', '.') }} VNĐ</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Tổng hoàn tiền
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_refund'], 0, ',', '.') }} VNĐ</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-undo fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Tổng giao dịch
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_transactions'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Ví đang hoạt động
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_wallets'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lọc giao dịch -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lọc giao dịch</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.transactions.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_id">Người dùng:</label>
                            <select name="user_id" id="user_id" class="form-control select2">
                                <option value="">-- Tất cả người dùng --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Loại giao dịch:</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">-- Tất cả --</option>
                                <option value="{{ \App\Models\WalletTransaction::TYPE_DEPOSIT }}" {{ request('type') == \App\Models\WalletTransaction::TYPE_DEPOSIT ? 'selected' : '' }}>
                                    Nạp tiền
                                </option>
                                <option value="{{ \App\Models\WalletTransaction::TYPE_PAYMENT }}" {{ request('type') == \App\Models\WalletTransaction::TYPE_PAYMENT ? 'selected' : '' }}>
                                    Thanh toán
                                </option>
                                <option value="{{ \App\Models\WalletTransaction::TYPE_REFUND }}" {{ request('type') == \App\Models\WalletTransaction::TYPE_REFUND ? 'selected' : '' }}>
                                    Hoàn tiền
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Từ ngày:</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Đến ngày:</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search fa-sm"></i> Lọc giao dịch
                        </button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt fa-sm"></i> Đặt lại
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="form-inline justify-content-end">
                            <div class="form-group">
                                <select name="sort" class="form-control form-control-sm mr-2">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Thời gian giao dịch</option>
                                    <option value="amount" {{ request('sort') == 'amount' ? 'selected' : '' }}>Số tiền</option>
                                </select>
                                <select name="order" class="form-control form-control-sm">
                                    <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary ml-2">
                                <i class="fas fa-sort fa-sm"></i> Sắp xếp
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lịch sử giao dịch -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lịch sử giao dịch</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Loại giao dịch</th>
                            <th>Số tiền</th>
                            <th>Số dư trước</th>
                            <th>Số dư sau</th>
                            <th>Mô tả</th>
                            <th>Tham chiếu</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>
                                <a href="{{ route('admin.wallets.show', $transaction->wallet->id) }}">
                                    {{ $transaction->wallet->user->name }}
                                </a>
                                <small class="d-block text-muted">{{ $transaction->wallet->user->email }}</small>
                            </td>
                            <td>
                                @if($transaction->type == \App\Models\WalletTransaction::TYPE_DEPOSIT)
                                <span class="badge badge-success">Nạp tiền</span>
                                @elseif($transaction->type == \App\Models\WalletTransaction::TYPE_PAYMENT)
                                <span class="badge badge-danger">Thanh toán</span>
                                @elseif($transaction->type == \App\Models\WalletTransaction::TYPE_REFUND)
                                <span class="badge badge-info">Hoàn tiền</span>
                                @else
                                <span class="badge badge-secondary">{{ $transaction->type }}</span>
                                @endif
                            </td>
                            <td class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ
                            </td>
                            <td>{{ number_format($transaction->balance_before, 0, ',', '.') }} VNĐ</td>
                            <td>{{ number_format($transaction->balance_after, 0, ',', '.') }} VNĐ</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if($transaction->reference_type && $transaction->reference_id)
                                <small>
                                    {{ $transaction->reference_type }} #{{ $transaction->reference_id }}
                                    @if($transaction->reference_type == 'order')
                                    <a href="{{ route('admin.orders.show', $transaction->reference_id) }}" class="btn btn-sm btn-link p-0 ml-1">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @elseif($transaction->reference_type == 'boosting_order')
                                    <a href="{{ route('admin.boosting_orders.show', $transaction->reference_id) }}" class="btn btn-sm btn-link p-0 ml-1">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @endif
                                </small>
                                @else
                                <small class="text-muted">Không có</small>
                                @endif
                            </td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.transactions.delete', $transaction->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giao dịch này? Hệ thống sẽ tự động hoàn tác số dư.')">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('admin.wallets.show', $transaction->wallet->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Khởi tạo select2 cho dropdown người dùng
        $('.select2').select2({
            placeholder: "Chọn người dùng",
            allowClear: true
        });
    });
</script>
@endsection 