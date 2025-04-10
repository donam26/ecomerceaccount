<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

// Các route này nên được đặt trong admin group để có prefix admin.
// Lưu ý: Điều này chỉ là tham khảo, cần include vào web.php

// Quản lý ví người dùng
Route::post('users/{id}/create-wallet', [AdminUserController::class, 'createWallet'])->name('users.create-wallet');
Route::get('users/{id}/wallet-adjust', [AdminUserController::class, 'showWalletAdjustForm'])->name('users.wallet-adjust');
Route::post('users/{id}/wallet-adjust', [AdminUserController::class, 'adjustWallet'])->name('users.wallet-adjust.submit');
Route::get('users/{id}/wallet-transactions', [AdminUserController::class, 'showWalletTransactions'])->name('users.wallet-transactions');
Route::post('users/{id}/toggle-wallet-status', [AdminUserController::class, 'toggleWalletStatus'])->name('users.toggle-wallet-status'); 