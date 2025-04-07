@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng cày thuê')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Chi tiết đơn hàng cày thuê</h1>
            
            <a href="{{ route('admin.boosting_orders.index') }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Thông tin đơn hàng -->
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Thông tin cơ bản -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin đơn hàng</h2>
                </div>
                <div class="px-6 py-4 divide-y divide-gray-200">
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Mã đơn hàng:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Trạng thái:</span>
                        <div>
                            @if($order->status == 'pending')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                Chờ thanh toán
                            </span>
                            @elseif($order->status == 'paid')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                Đã thanh toán
                            </span>
                            @elseif($order->status == 'processing')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
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
                            @endif
                        </div>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Giá trị đơn hàng:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($order->amount, 0, ',', '.') }}đ</span>
                    </div>
                    
                    @if($order->discount > 0)
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Giá gốc:</span>
                        <span class="text-sm text-gray-900">{{ number_format($order->original_amount, 0, ',', '.') }}đ</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Giảm giá:</span>
                        <span class="text-sm text-gray-900">{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Ngày tạo:</span>
                        <span class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    @if($order->completed_at)
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Ngày hoàn thành:</span>
                        <span class="text-sm text-gray-900">{{ $order->completed_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Thông tin khách hàng và dịch vụ -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin khách hàng</h2>
                </div>
                <div class="px-6 py-4 divide-y divide-gray-200">
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Tên khách hàng:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $order->user->name }}</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <span class="text-sm text-gray-900">{{ $order->user->email }}</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Số điện thoại:</span>
                        <span class="text-sm text-gray-900">{{ $order->user->phone ?? 'Không có' }}</span>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin dịch vụ</h2>
                </div>
                <div class="px-6 py-4 divide-y divide-gray-200">
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Tên dịch vụ:</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $order->service->name }}</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Game:</span>
                        <span class="text-sm text-gray-900">{{ $order->service->game->name }}</span>
                    </div>
                    
                    <div class="py-3 flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Thời gian hoàn thành ước tính:</span>
                        <span class="text-sm text-gray-900">{{ $order->service->estimated_days }} ngày</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin tài khoản và cập nhật trạng thái -->
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Thông tin tài khoản game -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Thông tin tài khoản game</h2>
                </div>
                <div class="px-6 py-4">
                    @if($order->hasAccountInfo())
                        <div class="mb-4">
                            <a href="{{ route('admin.boosting_orders.account', $order->id) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Xem thông tin tài khoản
                            </a>
                        </div>
                    @else
                        <div class="p-4 rounded-md bg-red-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Thông tin tài khoản chưa được cung cấp</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Khách hàng chưa cung cấp thông tin tài khoản game để thực hiện dịch vụ.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Thay đổi trạng thái -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Cập nhật trạng thái</h2>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.boosting_orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái mới</label>
                            <select id="status" name="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Giao đơn hàng cho nhân viên -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Giao đơn hàng</h2>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.boosting_orders.assign', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700">Giao cho nhân viên</label>
                            <select id="assigned_to" name="assigned_to" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ $order->assigned_to == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Giao đơn hàng
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Cập nhật ghi chú -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Ghi chú nội bộ</h2>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.boosting_orders.notes', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                            <textarea id="admin_notes" name="admin_notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nhập ghi chú nội bộ cho đơn hàng này">{{ $order->admin_notes }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Ghi chú này chỉ hiển thị cho quản trị viên và nhân viên.</p>
                        </div>
                        
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cập nhật ghi chú
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Thông tin thanh toán -->
        @if($transactions->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Lịch sử thanh toán</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mã giao dịch
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Phương thức
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số tiền
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày giao dịch
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->transaction_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->payment_method }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($transaction->amount, 0, ',', '.') }}đ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->status == 'completed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Thành công
                                </span>
                                @elseif($transaction->status == 'pending')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Đang xử lý
                                </span>
                                @elseif($transaction->status == 'failed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                    Thất bại
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 