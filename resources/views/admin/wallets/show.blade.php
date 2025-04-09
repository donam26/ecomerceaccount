@extends('admin.layouts.app')

@section('title', 'Chi tiết ví điện tử - ' . $wallet->user->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết ví điện tử</h1>
        <div>
            <a href="{{ route('admin.wallets.adjust', $wallet->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-money-bill"></i> Điều chỉnh số dư
            </a>
            <a href="{{ route('admin.wallets.edit', $wallet->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <a href="{{ route('admin.wallets.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
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

    <div class="row">
        <!-- Thông tin chung -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Thông tin ví điện tử
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ</div>
                            
                            <hr>
                            
                            <div class="text-xs font-weight-bold text-uppercase mb-1">ID Ví</div>
                            <div class="mb-2">{{ $wallet->id }}</div>
                            
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Người dùng</div>
                            <div class="mb-2">
                                <a href="{{ route('admin.users.edit', $wallet->user->id) }}">
                                    {{ $wallet->user->name }}
                                </a>
                                <div class="small text-muted">{{ $wallet->user->email }}</div>
                            </div>
                            
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Trạng thái</div>
                            <div class="mb-2">
                                @if($wallet->is_active)
                                <span class="badge badge-success">Hoạt động</span>
                                @else
                                <span class="badge badge-danger">Bị khóa</span>
                                @endif
                            </div>
                            
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Ngày tạo</div>
                            <div class="mb-2">{{ $wallet->created_at->format('d/m/Y H:i') }}</div>
                            
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Cập nhật cuối</div>
                            <div class="mb-2">{{ $wallet->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="col-xl-8 col-md-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê giao dịch</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
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

                        <div class="col-xl-3 col-md-6 mb-4">
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

                        <div class="col-xl-3 col-md-6 mb-4">
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

                        <div class="col-xl-3 col-md-6 mb-4">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử giao dịch -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lịch sử giao dịch</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Tùy chọn:</div>
                    <a class="dropdown-item" href="{{ route('admin.wallets.adjust', $wallet->id) }}">Điều chỉnh số dư</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
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