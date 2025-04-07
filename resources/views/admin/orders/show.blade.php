@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Chi tiết đơn hàng #{{ $order->id }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Thông tin đơn hàng -->
            <div class="md:col-span-2 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin đơn hàng</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Mã đơn hàng</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->order_number }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tổng tiền</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ number_format($order->total_amount, 0, ',', '.') }} đ</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Trạng thái</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
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
                            </dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Phương thức thanh toán</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->payment_method }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ngày đặt hàng</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Cập nhật lần cuối</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->updated_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        
                        <!-- Ghi chú -->
                        @if($order->notes)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Ghi chú</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->notes }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            
            <!-- Thông tin khách hàng -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin khách hàng</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tên</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->name }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->email }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Đã đăng ký</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tổng đơn hàng</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->orders->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
        
        <!-- Chi tiết tài khoản đã mua -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Chi tiết tài khoản đã mua</h3>
            </div>
            
            <div class="border-t border-gray-200">
                @if($order->account)
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row">
                            @php
                                $accountImage = 'https://via.placeholder.com/300x200';
                                if ($order->account->images) {
                                    if (is_string($order->account->images)) {
                                        $images = json_decode($order->account->images, true);
                                        if (is_array($images) && !empty($images)) {
                                            $accountImage = asset('storage/' . $images[0]);
                                        }
                                    } elseif (is_array($order->account->images) && !empty($order->account->images)) {
                                        $accountImage = asset('storage/' . $order->account->images[0]);
                                    }
                                }
                            @endphp
                            
                            <div class="md:w-1/4">
                                <img src="{{ $accountImage }}" alt="{{ $order->account->title }}" class="h-40 w-full object-cover rounded-md">
                            </div>
                            
                            <div class="md:w-3/4 md:pl-6 mt-4 md:mt-0">
                                <h4 class="text-lg font-bold">{{ $order->account->title }}</h4>
                                <div class="mt-2 text-sm text-gray-600">{{ $order->account->description }}</div>
                                
                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500">Game</h5>
                                        <p class="text-sm">{{ $order->account->game->name }}</p>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500">Trạng thái</h5>
                                        <p class="text-sm">
                                            @if($order->account->status == 'available')
                                                <span class="text-green-600">Có sẵn</span>
                                            @elseif($order->account->status == 'sold')
                                                <span class="text-red-600">Đã bán</span>
                                            @elseif($order->account->status == 'pending')
                                                <span class="text-yellow-600">Đang xử lý</span>
                                            @else
                                                {{ $order->account->status }}
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500">Giá</h5>
                                        <p class="text-sm">{{ number_format($order->account->price, 0, ',', '.') }} đ</p>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-500">Tên đăng nhập</h5>
                                        <p class="text-sm font-mono bg-gray-100 p-1 rounded">{{ $order->account->username }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-500">Mật khẩu</h5>
                                    <p class="text-sm font-mono bg-gray-100 p-1 rounded">{{ $order->account->password }}</p>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('admin.accounts.show', $order->account->id) }}" class="text-blue-600 hover:text-blue-800">
                                        Xem chi tiết tài khoản
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        Không tìm thấy thông tin tài khoản đã mua
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Lịch sử giao dịch -->
        @if($order->transactions && $order->transactions->count() > 0)
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Lịch sử giao dịch</h3>
                </div>
                <div class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã giao dịch</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaction->transaction_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->payment_method }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($transaction->status == 'completed')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Thành công
                                            </span>
                                        @elseif($transaction->status == 'pending')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                Chờ xử lý
                                            </span>
                                        @elseif($transaction->status == 'failed')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                Thất bại
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                {{ $transaction->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($transaction->amount, 0, ',', '.') }} đ</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Cập nhật trạng thái đơn hàng -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Cập nhật trạng thái đơn hàng</h3>
            </div>
            <div class="border-t border-gray-200 p-6">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $order->notes }}</textarea>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Cập nhật trạng thái
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 