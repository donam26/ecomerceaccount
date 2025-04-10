<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WalletDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'deposit_code',
        'amount',
        'payment_method',
        'status',
        'payment_content',
        'transaction_id',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'metadata' => 'json',
        'completed_at' => 'datetime',
    ];

    /**
     * Các trạng thái nạp tiền
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Tạo mã nạp tiền duy nhất
     * 
     * @return string
     */
    public static function generateDepositCode()
    {
        return 'WALLET-' . time() . rand(1000, 9999);
    }

    /**
     * Lấy người dùng sở hữu giao dịch
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy ví liên quan đến giao dịch
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Lấy transaction (nếu có) liên quan đến nạp tiền
     */
    public function transaction()
    {
        return $this->hasOne(WalletTransaction::class, 'reference_id')
            ->where('reference_type', 'wallet_deposit');
    }

    /**
     * Kiểm tra giao dịch đã hoàn thành
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Kiểm tra giao dịch đang chờ
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Kiểm tra giao dịch thất bại
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Đánh dấu giao dịch là đã hoàn thành
     */
    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = Carbon::now();
        $this->save();
        
        return $this;
    }
}
