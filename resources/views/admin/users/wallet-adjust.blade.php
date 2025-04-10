@extends('layouts.admin')

@section('title', 'Điều chỉnh số dư ví')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Điều chỉnh số dư ví: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.show', $user->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                Quay lại
            </a>
        </div>
        
        <!-- Thông báo -->
        @if(session('success'))
        <div class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <div class="font-medium">Đã xảy ra lỗi:</div>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <!-- Form điều chỉnh số dư -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin số dư hiện tại</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5">
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600">Số dư hiện tại</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($user->wallet->balance, 0, ',', '.') }} đ</p>
                        </div>

                        <form action="{{ route('admin.users.wallet-adjust.submit', $user->id) }}" method="POST" class="mt-6">
                            @csrf
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Loại điều chỉnh <span class="text-red-600">*</span>
                                    </label>
                                    <select id="type" name="type" required class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="add">Thêm tiền vào ví</option>
                                        <option value="subtract">Trừ tiền từ ví</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                        Số tiền (VNĐ) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="number" id="amount" name="amount" required min="1000" step="1000"
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Nhập số tiền cần điều chỉnh">
                                    <p class="text-xs text-gray-500 mt-1">Số tiền tối thiểu: 1.000 VNĐ</p>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Lý do điều chỉnh <span class="text-red-600">*</span>
                                    </label>
                                    <textarea id="description" name="description" rows="3" required
                                        class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Nhập lý do điều chỉnh số dư"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Xác nhận điều chỉnh
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div>
                <!-- Thông tin người dùng -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin người dùng</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5">
                        <dl class="divide-y divide-gray-200">
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="text-sm text-gray-900">{{ $user->id }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Họ tên</dt>
                                <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Trạng thái ví</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($user->wallet->is_active)
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
                        </dl>
                    </div>
                </div>

                <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Lưu ý</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5">
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-2">
                            <li>Điều chỉnh số dư sẽ tạo giao dịch trong lịch sử ví người dùng</li>
                            <li>Nếu trừ tiền, hãy đảm bảo số dư người dùng đủ</li>
                            <li>Hành động này không thể hoàn tác, vui lòng kiểm tra kỹ thông tin</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 