<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// SePay Webhook
Route::post('webhooks/sepay', [WebhookController::class, 'sepay']);

// Thêm route xử lý webhook từ TheSieuRe
Route::post('/thesieure/webhook', [WalletController::class, 'theSieuReWebhook'])->name('api.thesieure.webhook');

// Webhook routes
Route::post('webhooks/thesieure', [WalletController::class, 'theSieuReWebhook']);
