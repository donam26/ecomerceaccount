@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chi tiết người dùng: {{ $user->name }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Chỉnh sửa
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Quay lại
                </a>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <!-- Thông tin người dùng -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin người dùng</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->id }}</dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Họ tên</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->name }}</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->email }}</dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Vai trò</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    @if($user->role)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">Không có vai trò</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                    @if($user->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Hoạt động
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            Bị khóa
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Ngày đăng ký</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->created_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $user->updated_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <!-- Thông tin ví điện tử -->
                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Ví điện tử</h3>
                        <div class="flex space-x-2">
                            @if(!$wallet)
                                <form action="{{ route('admin.users.create-wallet', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 text-sm rounded-md hover:bg-green-700">
                                        Tạo ví mới
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.users.wallet-adjust', $user->id) }}" class="bg-blue-600 text-white px-3 py-1 text-sm rounded-md hover:bg-blue-700">
                                    Điều chỉnh số dư
                                </a>
                                <form action="{{ route('admin.users.toggle-wallet-status', $user->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="{{ $wallet->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1 text-sm rounded-md">
                                        {{ $wallet->is_active ? 'Khóa ví' : 'Mở khóa ví' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-gray-200">
                        @if($wallet)
                            <dl>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">ID ví</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $wallet->id }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Số dư</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-semibold">
                                        {{ number_format($wallet->balance, 0, ',', '.') }} đ
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        @if($wallet->is_active)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Đang hoạt động
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Bị khóa
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Ngày tạo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $wallet->created_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                            </dl>

                            <!-- Giao dịch gần đây -->
                            <div class="mt-4 border-t border-gray-200">
                                <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                                    <h4 class="text-md font-medium text-gray-700">Giao dịch gần đây</h4>
                                    <a href="{{ route('admin.users.wallet-transactions', $user->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                        Xem tất cả
                                    </a>
                                </div>
                                
                                @if(count($walletTransactions) > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Thời gian
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Loại
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Số tiền
                                                    </th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Mô tả
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($walletTransactions as $transaction)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if($transaction->isDeposit())
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                                    Nạp tiền
                                                                </span>
                                                            @elseif($transaction->isWithdraw())
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                                    Rút tiền
                                                                </span>
                                                            @elseif($transaction->isPayment())
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                                    Thanh toán
                                                                </span>
                                                            @elseif($transaction->isRefund())
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                                    Hoàn tiền
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 0, ',', '.') }} đ
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $transaction->description ?? 'Không có mô tả' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-6 text-center text-gray-500">
                                        Chưa có giao dịch nào
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6 text-center text-gray-500">
                                Người dùng chưa có ví. Hãy tạo ví mới cho người dùng này.
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Lịch sử đơn hàng -->
                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Lịch sử đơn hàng</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        @if($user->orders->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($user->orders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($order->status == 'pending')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                        Chờ thanh toán
                                                    </span>
                                                @elseif($order->status == 'processing')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                        Đang xử lý
                                                    </span>
                                                @elseif($order->status == 'completed')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                        Hoàn thành
                                                    </span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                        Đã hủy
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                        {{ $order->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-6 text-center text-gray-500">
                                Người dùng chưa có đơn hàng nào
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <!-- Thống kê -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tổng quan</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-5">
                            <div class="bg-blue-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="truncate text-sm font-medium text-blue-700">Tổng đơn hàng</dt>
                                <dd class="mt-1 text-3xl font-semibold text-blue-900">{{ $user->orders->count() }}</dd>
                            </div>
                            
                            <div class="bg-green-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="truncate text-sm font-medium text-green-700">Đơn hàng thành công</dt>
                                <dd class="mt-1 text-3xl font-semibold text-green-900">{{ $user->orders->where('status', 'completed')->count() }}</dd>
                            </div>
                            
                            <div class="bg-yellow-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="truncate text-sm font-medium text-yellow-700">Đơn hàng đang xử lý</dt>
                                <dd class="mt-1 text-3xl font-semibold text-yellow-900">
                                    {{ $user->orders->whereIn('status', ['pending', 'processing'])->count() }}
                                </dd>
                            </div>
                            
                            <div class="bg-indigo-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="truncate text-sm font-medium text-indigo-700">Tổng chi tiêu</dt>
                                <dd class="mt-1 text-3xl font-semibold text-indigo-900">
                                    {{ number_format($user->orders->where('status', 'completed')->sum('total_amount'), 0, ',', '.') }} đ
                                </dd>
                            </div>
                            
                            @if($wallet)
                            <div class="bg-purple-50 overflow-hidden rounded-lg px-4 py-5 sm:p-6">
                                <dt class="truncate text-sm font-medium text-purple-700">Số dư ví</dt>
                                <dd class="mt-1 text-3xl font-semibold text-purple-900">
                                    {{ number_format($wallet->balance, 0, ',', '.') }} đ
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                
                <!-- Khóa / Mở tài khoản -->
                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thao tác</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            
                            @if($user->is_active)
                                <input type="hidden" name="is_active" value="0">
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                    Khóa tài khoản
                                </button>
                            @else
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Mở khóa tài khoản
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 