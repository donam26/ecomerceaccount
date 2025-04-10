@extends('layouts.admin')

@section('title', 'Lịch sử giao dịch ví')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Lịch sử giao dịch ví: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.show', $user->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>

        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin ví</h3>
            </div>
            <div class="border-t border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-700">ID ví</p>
                        <p class="text-xl font-semibold">#{{ $user->wallet->id }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-green-700">Số dư hiện tại</p>
                        <p class="text-xl font-semibold text-green-600">{{ number_format($user->wallet->balance, 0, ',', '.') }} đ</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-sm text-purple-700">Trạng thái</p>
                        <p class="text-xl font-semibold">
                            @if($user->wallet->is_active)
                                <span class="text-green-600">Đang hoạt động</span>
                            @else
                                <span class="text-red-600">Bị khóa</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử giao dịch -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Lịch sử giao dịch ví</h3>
            </div>
            <div class="border-t border-gray-200">
                @if(count($transactions) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
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
                                        Số dư trước
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số dư sau
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mô tả
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->id }}
                                        </td>
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
                                            {{ number_format($transaction->balance_before, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ number_format($transaction->balance_after, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->description ?? 'Không có mô tả' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        Chưa có giao dịch nào
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 