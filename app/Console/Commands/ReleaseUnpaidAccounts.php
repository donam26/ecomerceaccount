<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReleaseUnpaidAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:release-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Giải phóng các tài khoản đã đặt giữ nhưng không thanh toán';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $this->info('Bắt đầu xử lý vào: ' . $startTime->format('Y-m-d H:i:s'));

        // Tìm tài khoản hết hạn sử dụng SQL trực tiếp để đảm bảo so sánh chính xác
        $expiredAccountIds = DB::select(
            "SELECT id FROM accounts 
             WHERE status = 'pending' 
             AND reserved_until IS NOT NULL
             AND reserved_until < NOW()"
        );
        
        if (empty($expiredAccountIds)) {
            $this->info('Không có tài khoản nào cần giải phóng.');
            return 0;
        }
        
        // Lấy danh sách ID từ kết quả truy vấn
        $accountIds = array_map(function($account) {
            return $account->id;
        }, $expiredAccountIds);
        
        // Lấy đầy đủ thông tin tài khoản
        $expiredAccounts = Account::whereIn('id', $accountIds)->get();
        
        $this->info('Tìm thấy ' . count($expiredAccounts) . ' tài khoản cần giải phóng.');
        
        // Để debug: Hiển thị danh sách tài khoản và thời gian
        foreach ($expiredAccounts as $account) {
            $this->line("- ID: {$account->id}, Title: {$account->title}, Reserved Until: {$account->reserved_until}");
        }
        
        $releasedCount = 0;
        $errors = 0;
        
        foreach ($expiredAccounts as $account) {
            // Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
            DB::beginTransaction();
            
            try {
                // Tìm các đơn hàng pending liên quan đến tài khoản này
                $pendingOrders = Order::where('account_id', $account->id)
                    ->where('status', 'pending')
                    ->get();
                
                // Cập nhật trạng thái các đơn hàng thành cancelled
                foreach ($pendingOrders as $order) {
                    $order->status = 'cancelled';
                    $order->cancelled_at = Carbon::now();
                    $order->save();
                    
                    $this->info("- Đơn hàng #{$order->order_number} tự động hủy do quá thời gian thanh toán.");
                
                }
                
                // Giải phóng tài khoản (sử dụng update trực tiếp thay vì qua model để tránh vấn đề về timezone)
                DB::table('accounts')
                    ->where('id', $account->id)
                    ->update([
                        'status' => 'available',
                        'reserved_until' => null,
                        'updated_at' => Carbon::now()
                    ]);
                
                $this->info("- Tài khoản #{$account->id} ({$account->title}) đã được giải phóng.");
                
                DB::commit();
                $releasedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                
                $this->error("Lỗi khi giải phóng tài khoản #{$account->id}: " . $e->getMessage());
          
            }
        }
        
        $endTime = Carbon::now();
        $duration = $endTime->diffInSeconds($startTime);
        
        $this->info("Hoàn thành: Đã giải phóng $releasedCount/" . count($expiredAccounts) . " tài khoản trong $duration giây.");
        
        if ($errors > 0) {
            $this->warn("Có $errors lỗi xảy ra trong quá trình xử lý.");
            return 1;
        }
        
        return 0;
    }
}
