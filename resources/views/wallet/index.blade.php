@extends('layouts.app')

@section('title', 'Ví điện tử')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Ví điện tử của tôi</h1>
        
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
        @endif
        
        <!-- Thông tin số dư và nút nạp tiền -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Số dư hiện tại</h2>
                        <div class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($wallet->balance, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('wallet.deposit') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nạp tiền
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Giao dịch gần đây -->
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Giao dịch gần đây</h2>
        
        @if($transactions->count() > 0)
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại giao dịch</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số dư sau</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->isDeposit())
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Nạp tiền</span>
                                @elseif($transaction->isPayment())
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Thanh toán</span>
                                @elseif($transaction->isRefund())
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Hoàn tiền</span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $transaction->type }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="{{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount, 0, ',', '.') }} VNĐ
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($transaction->balance_after, 0, ',', '.') }} VNĐ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        </div>
        
        <!-- <div class="flex justify-between items-center">
            <a href="{{ route('wallet.transactions') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Xem tất cả giao dịch
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div> -->
        @else
        <div class="bg-white shadow-md rounded-lg p-6 text-center">
            <p class="text-gray-500">Bạn chưa có giao dịch nào.</p>
        </div>
        @endif
    </div>
</div>
@endsection 