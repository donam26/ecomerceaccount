            <li class="relative">
                <a href="{{ route('admin.boosting_orders.index') }}" class="flex items-center py-2 px-3 hover:bg-blue-500 hover:text-white {{ request()->routeIs('admin.boosting_orders.*') ? 'bg-blue-500 text-white' : 'text-gray-600' }} rounded-md mb-1">
                    <i class="fas fa-calendar-check mr-3"></i>
                    <span>Đơn hàng cày thuê</span>
                </a>
            </li>
            
            <!-- Quản lý nạp thuê -->
            <li class="relative mb-2">
                <span class="block text-xs uppercase text-gray-400 tracking-wide px-3 py-1 mb-1">Nạp thuê</span>
            </li>
            <li class="relative">
                <a href="{{ route('admin.topup.index') }}" class="flex items-center py-2 px-3 hover:bg-blue-500 hover:text-white {{ request()->routeIs('admin.topup.*') ? 'bg-blue-500 text-white' : 'text-gray-600' }} rounded-md mb-1">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    <span>Dịch vụ nạp thuê</span>
                </a>
            </li>
            <li class="relative">
                <a href="{{ route('admin.topup_orders.index') }}" class="flex items-center py-2 px-3 hover:bg-blue-500 hover:text-white {{ request()->routeIs('admin.topup_orders.*') ? 'bg-blue-500 text-white' : 'text-gray-600' }} rounded-md mb-1">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Đơn hàng nạp thuê</span>
                </a>
            </li>
            
            <!-- Quản lý ví điện tử --> 