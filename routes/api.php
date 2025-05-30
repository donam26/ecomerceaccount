<?php

use App\Http\Controllers\WalletController;
use App\Http\Controllers\WebhookTheSieuReController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

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
Route::post('webhooks/sepay', [WebhookController::class, 'sepay'])->name('api.sepay.webhook');

// TheSieuRe Webhook
Route::post('webhook/thesieure', [WebhookTheSieuReController::class, 'handleCallback'])->name('callback.thesieure');
