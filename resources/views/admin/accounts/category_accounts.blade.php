@extends('layouts.admin')

@section('title', 'Tài khoản trong danh mục ' . $category->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                Tài khoản trong danh mục: <span class="text-blue-600">{{ $category->name }}</span>
            </h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.accounts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
                <a href="{{ route('admin.accounts.create') }}?category_id={{ $category->id }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Thêm tài khoản mới
                </a>
            </div>
        </div>

        <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Thông tin danh mục</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-32 object-cover rounded-md">
                            @else
                                <div class="w-full h-32 bg-gray-200 flex items-center justify-center rounded-md">
                                    <span class="text-gray-500 text-xs">Không có ảnh</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <p><span class="font-medium">ID:</span> {{ $category->id }}</p>
                            <p><span class="font-medium">Tên:</span> {{ $category->name }}</p>
                            <p><span class="font-medium">Slug:</span> {{ $category->slug }}</p>
                            <p><span class="font-medium">Trạng thái:</span> 
                                @if($category->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Hoạt động</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Không hoạt động</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="font-medium">Mô tả:</p>
                        <p class="text-gray-600">{{ $category->description ?? 'Không có mô tả' }}</p>
                    </div>
                </div>
                <div>
                    <!-- Bộ lọc -->
                    <form action="{{ route('admin.accounts.category', $category->id) }}" method="GET" class="p-4 bg-gray-50 rounded-md">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Lọc tài khoản</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="game_id" class="block text-sm font-medium text-gray-700">Trò chơi</label>
                                <select id="game_id" name="game_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Tất cả trò chơi</option>
                                    @foreach($games as $game)
                                        <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                                <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Có sẵn</option>
                                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Đã bán</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Danh sách tài khoản -->
        <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hình ảnh
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tiêu đề
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trò chơi
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($accounts as $account)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $account->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!empty($account->images) && is_array($account->images) && isset($account->images[0]))
                                    <img src="{{ Storage::url($account->images[0]) }}" alt="{{ $account->title }}" class="h-10 w-10 rounded-full object-cover">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">No img</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $account->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $account->game->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($account->original_price && $account->original_price > $account->price)
                                    <span class="line-through">{{ number_format($account->original_price, 0, ',', '.') }}đ</span>
                                    <span class="text-red-600 font-medium">{{ number_format($account->price, 0, ',', '.') }}đ</span>
                                @else
                                    <span>{{ number_format($account->price, 0, ',', '.') }}đ</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($account->status == 'available')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Có sẵn
                                    </span>
                                @elseif($account->status == 'sold')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        Đã bán
                                    </span>
                                @elseif($account->status == 'pending')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Đang xử lý
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ $account->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.accounts.edit', $account->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    Sửa
                                </a>
                                
                                <form action="{{ route('admin.accounts.destroy', $account->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Không có tài khoản nào trong danh mục này
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4">
                {{ $accounts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 