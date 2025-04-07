<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use SePay\SePay\Events\SePayWebhookEvent;
use Illuminate\Support\Facades\Log;

class SePayWebhookListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SePayWebhookEvent $event): void
    {
        Log::info('Nhận Sepay Webhook', [
            'info' => $event->info,
            'transferType' => $event->sePayWebhookData->transferType,
            'amount' => $event->sePayWebhookData->transferAmount,
            'content' => $event->sePayWebhookData->content,
        ]);

        // Xử lý tiền vào tài khoản
        if ($event->sePayWebhookData->transferType === 'in') {
            // Trường hợp $event->info là order_id 
            $order = Order::find($event->info);
            
            if ($order) {
                // Kiểm tra số tiền
                if ($order->amount != $event->sePayWebhookData->transferAmount) {
                    Log::warning('Số tiền thanh toán không khớp', [
                        'order_id' => $order->id,
                        'expected' => $order->amount,
                        'received' => $event->sePayWebhookData->transferAmount,
                    ]);
                    return;
                }

                // Nếu đơn hàng đang chờ thanh toán, cập nhật thành đã hoàn thành
                if ($order->status === 'pending') {
                    $order->status = 'completed';
                    $order->paid_at = Carbon::now();
                    $order->completed_at = Carbon::now();
                    
                    // Lưu thông tin tài khoản game, trong thực tế dữ liệu này cần mã hóa
                    $accountDetails = [
                        'username' => $order->account->login_username,
                        'password' => $order->account->login_password,
                        'extra_info' => $order->account->extra_info ?? null,
                    ];
                    
                    $order->account_details = json_encode($accountDetails);
                    $order->save();
                    
                    // Đánh dấu tài khoản đã bán
                    $order->account->status = 'sold';
                    $order->account->sold_at = Carbon::now();
                    $order->account->save();
                    
                    // Tạo giao dịch
                    Transaction::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'amount' => $event->sePayWebhookData->transferAmount,
                        'type' => 'payment',
                        'status' => 'completed',
                        'payment_method' => 'sepay',
                        'reference_id' => $event->sePayWebhookData->referenceCode,
                        'description' => 'Thanh toán đơn hàng #' . $order->order_number,
                        'payment_details' => json_encode($event->sePayWebhookData),
                    ]);

                    Log::info('Đơn hàng đã được thanh toán thành công', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ]);
                }
            } else {
                Log::warning('Không tìm thấy đơn hàng với ID ' . $event->info);
            }
        } else {
            // Xử lý tiền ra tài khoản (nếu cần)
            Log::info('Giao dịch tiền ra', [
                'data' => $event->sePayWebhookData,
            ]);
        }
    }
} 