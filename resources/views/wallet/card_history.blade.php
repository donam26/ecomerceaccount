@extends('layouts.app')

@section('title', 'Lịch sử nạp thẻ cào')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Lịch sử nạp thẻ cào</h1>
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại ví
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Tổng tiền nạp thành công: <span class="text-green-600">{{ number_format($stats['total_amount']) }}đ</span></h3>

                @if(count($cardDeposits) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhà mạng</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mệnh giá</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thực nhận</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã thẻ</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cardDeposits as $deposit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $deposit->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $deposit->telco }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($deposit->amount) }}đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($deposit->status == \App\Models\CardDeposit::STATUS_COMPLETED)
                                                <span class="text-green-600 font-medium">{{ number_format($deposit->actual_amount) }}đ</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                <div class="text-xs text-gray-500">Serial: {{ substr($deposit->serial, 0, 4) }}...{{ substr($deposit->serial, -4) }}</div>
                                                <div class="text-xs text-gray-500">Code: {{ substr($deposit->code, 0, 4) }}...{{ substr($deposit->code, -4) }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($deposit->status == \App\Models\CardDeposit::STATUS_COMPLETED)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Thành công
                                                </span>
                                            @elseif($deposit->status == \App\Models\CardDeposit::STATUS_FAILED)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Thất bại
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Đang xử lý
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($deposit->status == \App\Models\CardDeposit::STATUS_PENDING)
                                                <a href="{{ route('wallet.card.pending', $deposit->request_id) }}" class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm border border-blue-300 hover:bg-blue-50">
                                                    Kiểm tra
                                                </a>
                                            @elseif($deposit->status == \App\Models\CardDeposit::STATUS_FAILED && isset($deposit->metadata['failure_reason']))
                                                <button type="button" class="text-red-600 hover:text-red-900 px-3 py-1 rounded-md text-sm border border-red-300 hover:bg-red-50" data-tooltip="{{ $deposit->metadata['failure_reason'] ?? 'Không xác định' }}">
                                                    Lý do lỗi
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $cardDeposits->links() }}
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Bạn chưa có giao dịch nạp thẻ nào. 
                                    <a href="{{ route('wallet.deposit') }}" class="font-medium underline text-blue-700 hover:text-blue-600">
                                        Nạp thẻ ngay
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý tooltip cho lý do lỗi
        const tooltipButtons = document.querySelectorAll('[data-tooltip]');
        tooltipButtons.forEach(button => {
            const tooltipText = button.getAttribute('data-tooltip');
            
            button.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.textContent = tooltipText;
                tooltip.className = 'absolute z-10 p-2 bg-gray-900 text-white text-xs rounded shadow-lg max-w-xs';
                tooltip.style.top = (this.offsetHeight + 5) + 'px';
                tooltip.style.left = '0';
                tooltip.id = 'active-tooltip';
                
                this.style.position = 'relative';
                this.appendChild(tooltip);
            });
            
            button.addEventListener('mouseleave', function() {
                const tooltip = document.getElementById('active-tooltip');
                if (tooltip) {
                    tooltip.remove();
                }
            });
        });
    });
</script>
@endpush 