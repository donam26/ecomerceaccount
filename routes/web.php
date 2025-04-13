<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BoostingServiceController;
use App\Http\Controllers\Admin\BoostingServiceController as AdminBoostingServiceController;
use App\Http\Controllers\Admin\BoostingOrderController as AdminBoostingOrderController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\TopUpServiceController;
use App\Http\Controllers\GameServiceController;
use App\Http\Controllers\Admin\GameServiceController as AdminGameServiceController;
use App\Http\Controllers\AccountCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Trang chủ và thông tin
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Game
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');

// Tài khoản
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::get('/accounts/search', [AccountController::class, 'search'])->name('accounts.search');
Route::get('/accounts/{id}', [AccountController::class, 'show'])->name('accounts.show');

// Danh mục tài khoản
Route::get('/account-categories', [AccountCategoryController::class, 'index'])->name('account.categories');
Route::get('/account-categories/{slug}', [AccountCategoryController::class, 'show'])->name('account.category');
Route::get('/account-categories/{slug}/filter', [AccountCategoryController::class, 'filter'])->name('account.category.filter');

    // Dịch vụ game
    Route::get('/services', [GameServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}', [GameServiceController::class, 'show'])->name('services.show');
// Yêu cầu đăng nhập
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Đơn hàng
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Thanh toán
    Route::get('/payment/checkout/{orderNumber}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success/{orderNumber}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/check-status/{orderNumber}', [PaymentController::class, 'checkStatus'])->name('payment.check_status');
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/payment/wallet/{orderNumber}', [PaymentController::class, 'processWalletPayment'])->name('payment.wallet');

    // Dịch vụ cày thuê
    Route::get('/boosting', [BoostingServiceController::class, 'index'])->name('boosting.index');
    
    // Thêm route để hiển thị danh sách đơn hàng cày thuê của người dùng
    Route::get('/boosting/my-orders', [BoostingServiceController::class, 'myOrders'])->name('boosting.my_orders');
    
    // Route đặt hàng phải đứng trước route show để được ưu tiên
    Route::post('/boosting/{slug}/order', [BoostingServiceController::class, 'order'])->name('boosting.order');
    Route::get('/boosting/{slug}', [BoostingServiceController::class, 'show'])->name('boosting.show');
    
    // Sửa URL để tránh xung đột với route của đơn hàng thường, mẫu /orders/... 
    Route::get('/boosting-orders/{orderNumber}', [BoostingServiceController::class, 'showOrder'])->name('boosting.orders.show');
    
    // Nhập thông tin tài khoản sau khi thanh toán - cập nhật đường dẫn thành /boosting-account-info thay vì /boosting/orders/...
    Route::get('/boosting-account-info/{orderNumber}', [BoostingServiceController::class, 'accountInfo'])
        ->name('boosting.account_info');
    Route::post('/boosting-account-info/{orderNumber}', [BoostingServiceController::class, 'submitAccountInfo'])
        ->name('boosting.account_info.submit');
    Route::get('/boosting-account-info/{orderNumber}/success', [BoostingServiceController::class, 'accountInfoSuccess'])
        ->name('boosting.account_info.success');

    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::get('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/deposit', [WalletController::class, 'processDeposit'])->name('wallet.deposit.process');
    Route::get('/wallet/deposit/callback', [WalletController::class, 'depositCallback'])->name('wallet.deposit.callback');
    
    // Thêm route xử lý nạp thẻ cào TheSieuRe
    Route::post('/wallet/deposit/card', [WalletController::class, 'depositCard'])->name('wallet.deposit.card');
    Route::get('/wallet/card/history', [WalletController::class, 'cardDepositHistory'])->name('wallet.card.history');
    Route::get('/wallet/card/{requestId}', [WalletController::class, 'showCardPending'])->name('wallet.card.pending');
    Route::get('/wallet/card/{requestId}/check', [WalletController::class, 'checkCardStatus'])->name('wallet.card.check');

    // Dịch vụ nạp thuê
    Route::get('/topup', [TopUpServiceController::class, 'index'])->name('topup.index');
    
    // Thêm route để hiển thị danh sách đơn hàng nạp thuê của người dùng
    Route::get('/topup/my-orders', [TopUpServiceController::class, 'myOrders'])->name('topup.my_orders');
    
    // Route đặt hàng phải đứng trước route show để được ưu tiên
    Route::post('/topup/{slug}/order', [TopUpServiceController::class, 'order'])->name('topup.order');
    Route::get('/topup/{slug}', [TopUpServiceController::class, 'show'])->name('topup.show');
    
    // Chi tiết đơn hàng
    Route::get('/topup-orders/{orderNumber}', [TopUpServiceController::class, 'showOrder'])->name('topup.orders.show');


    Route::post('/services/{slug}/order', [GameServiceController::class, 'order'])->name('services.order');
    Route::post('/services/{slug}/package/{package}', [GameServiceController::class, 'orderPackage'])->name('services.order_package');
    Route::get('/my-service-orders', [GameServiceController::class, 'myOrders'])->name('services.my_orders');
    Route::get('/services/my-orders/{orderNumber}', [GameServiceController::class, 'viewOrder'])->name('services.view_order');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý game
    Route::resource('games', AdminGameController::class);
    
    // Quản lý tài khoản game
    Route::resource('accounts', AdminAccountController::class);
    Route::get('accounts/category/{categoryId}', [AdminAccountController::class, 'getCategoryAccounts'])->name('accounts.category');
    
    // Quản lý danh mục tài khoản
    Route::resource('account_categories', \App\Http\Controllers\Admin\AccountCategoryController::class);
    
    // Quản lý đơn hàng
    Route::resource('orders', AdminOrderController::class);
    
    // Quản lý người dùng
    Route::resource('users', AdminUserController::class);
    
    // Quản lý ví người dùng
    Route::post('users/{id}/create-wallet', [AdminUserController::class, 'createWallet'])->name('users.create-wallet');
    Route::get('users/{id}/wallet-adjust', [AdminUserController::class, 'showWalletAdjustForm'])->name('users.wallet-adjust');
    Route::post('users/{id}/wallet-adjust', [AdminUserController::class, 'adjustWallet'])->name('users.wallet-adjust.submit');
    Route::get('users/{id}/wallet-transactions', [AdminUserController::class, 'showWalletTransactions'])->name('users.wallet-transactions');
    Route::post('users/{id}/toggle-wallet-status', [AdminUserController::class, 'toggleWalletStatus'])->name('users.toggle-wallet-status');

    // Quản lý dịch vụ cày thuê
    Route::resource('boosting', AdminBoostingServiceController::class);
    
    // Quản lý đơn hàng cày thuê
    Route::get('boosting-orders', [AdminBoostingOrderController::class, 'index'])->name('boosting_orders.index');
    Route::get('boosting-orders/{id}', [AdminBoostingOrderController::class, 'show'])->name('boosting_orders.show');
    Route::post('boosting-orders/{id}/assign', [AdminBoostingOrderController::class, 'assign'])->name('boosting_orders.assign');
    Route::post('boosting-orders/{id}/status', [AdminBoostingOrderController::class, 'updateStatus'])->name('boosting_orders.status');
    Route::post('boosting-orders/{id}/notes', [AdminBoostingOrderController::class, 'updateNotes'])->name('boosting_orders.notes');
    Route::post('boosting-orders/{id}/update-notes', [AdminBoostingOrderController::class, 'updateNotes'])->name('boosting_orders.update-notes');
    Route::get('boosting-orders/{id}/account', [AdminBoostingOrderController::class, 'viewGameAccount'])->name('boosting_orders.account');
    
    // Quản lý ví điện tử
    Route::get('wallets', [AdminWalletController::class, 'index'])->name('wallets.index');
    Route::get('wallets/{id}', [AdminWalletController::class, 'show'])->name('wallets.show');
    Route::get('wallets/{id}/edit', [AdminWalletController::class, 'edit'])->name('wallets.edit');
    Route::put('wallets/{id}', [AdminWalletController::class, 'update'])->name('wallets.update');
    Route::get('wallets/{id}/adjust', [AdminWalletController::class, 'showAdjustForm'])->name('wallets.adjust');
    Route::post('wallets/{id}/adjust', [AdminWalletController::class, 'adjustBalance'])->name('wallets.adjust.submit');
    Route::get('transactions', [AdminWalletController::class, 'allTransactions'])->name('transactions.index');
    Route::delete('transactions/{id}', [AdminWalletController::class, 'deleteTransaction'])->name('transactions.delete');
    
    // Quản lý dịch vụ nạp thuê
    Route::resource('topup', \App\Http\Controllers\Admin\TopUpServiceController::class);
    
    // Quản lý đơn hàng nạp thuê
    Route::get('topup-orders', [\App\Http\Controllers\Admin\TopUpOrderController::class, 'index'])->name('topup_orders.index');
    Route::get('topup-orders/{id}', [\App\Http\Controllers\Admin\TopUpOrderController::class, 'show'])->name('topup_orders.show');
    Route::post('topup-orders/{id}/assign', [\App\Http\Controllers\Admin\TopUpOrderController::class, 'assign'])->name('topup_orders.assign');
    Route::post('topup-orders/{id}/status', [\App\Http\Controllers\Admin\TopUpOrderController::class, 'updateStatus'])->name('topup_orders.status');
    Route::post('topup-orders/{id}/notes', [\App\Http\Controllers\Admin\TopUpOrderController::class, 'updateNotes'])->name('topup_orders.notes');

    // Game Services
    Route::resource('services', AdminGameServiceController::class);
    
    // Service Packages
    Route::get('services/{service}/packages', [AdminGameServiceController::class, 'packages'])->name('services.packages');
    Route::get('services/{service}/packages/create', [AdminGameServiceController::class, 'createPackage'])->name('services.packages.create');
    Route::post('services/{service}/packages', [AdminGameServiceController::class, 'storePackage'])->name('services.packages.store');
    Route::get('services/{service}/packages/{package}/edit', [AdminGameServiceController::class, 'editPackage'])->name('services.packages.edit');
    Route::put('services/{service}/packages/{package}', [AdminGameServiceController::class, 'updatePackage'])->name('services.packages.update');
    Route::delete('services/{service}/packages/{package}', [AdminGameServiceController::class, 'destroyPackage'])->name('services.packages.destroy');
    
    // Service Orders
    Route::get('service-orders', [AdminGameServiceController::class, 'orders'])->name('services.orders.index');
    Route::get('service-orders/{order}', [AdminGameServiceController::class, 'showOrder'])->name('services.orders.show');
    Route::put('service-orders/{order}/status', [AdminGameServiceController::class, 'updateOrderStatus'])->name('services.orders.update-status');
});

require __DIR__.'/auth.php';
