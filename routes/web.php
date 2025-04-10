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
    Route::get('/boosting-orders/{orderNumber}', [BoostingServiceController::class, 'show'])->name('boosting.orders.show');
    
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
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý game
    Route::resource('games', AdminGameController::class);
    
    // Quản lý tài khoản game
    Route::resource('accounts', AdminAccountController::class);
    
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
});

require __DIR__.'/auth.php';
