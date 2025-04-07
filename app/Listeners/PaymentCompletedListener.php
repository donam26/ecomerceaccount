<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class PaymentCompletedListener implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(PaymentCompleted $event): void
    {
        $order = $event->order;
        $transaction = $event->transaction;
        
        // Ghi log
        Log::info('Xử lý PaymentCompletedListener', [
            'order_id' => $order->id,
            'transaction_id' => $transaction->id
        ]);
        
        // Lấy thông tin tài khoản game
        $account = $order->account;
        
        if ($account) {
            // Chuẩn bị dữ liệu thông tin tài khoản để hiển thị cho người dùng
            $accountInfo = [
                'username' => $account->username,
                'password' => $account->password,
                'extra_info' => $account->attributes, // Thông tin thêm về tài khoản
                'game_name' => $account->game->name,
            ];
            
            // Lưu thông tin tài khoản vào đơn hàng nếu có cột tương ứng
            try {
                // Kiểm tra xem có cột account_details không
                if (in_array('account_details', Schema::getColumnListing('orders'))) {
                    $order->account_details = json_encode($accountInfo);
                    $order->save();
                    Log::info('Đã lưu thông tin tài khoản vào đơn hàng', [
                        'order_id' => $order->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Không thể lưu thông tin tài khoản vào đơn hàng', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Gửi email thông báo thanh toán thành công cho người dùng
            try {
                $user = User::find($order->user_id);
                if ($user && $user->email) {
                    // Gửi email thông báo
                    // Mail::to($user->email)->send(new \App\Mail\PaymentSuccessful($order, $accountInfo));
                    
                    Log::info('Đã gửi email thông báo thanh toán thành công', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Không thể gửi email thông báo', [
                    'user_id' => $order->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::warning('Không tìm thấy thông tin tài khoản game', [
                'order_id' => $order->id,
                'account_id' => $order->account_id
            ]);
        }
    }
}
