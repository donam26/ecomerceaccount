<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'reference_type',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'balance_before' => 'decimal:0',
        'balance_after' => 'decimal:0',
        'metadata' => 'json',
    ];

    /**
     * Các loại giao dịch
     */
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';

    /**
     * Lấy ví liên quan đến giao dịch
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Lấy người dùng thực hiện giao dịch
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra giao dịch có phải là nạp tiền
     */
    public function isDeposit()
    {
        return $this->type === self::TYPE_DEPOSIT;
    }

    /**
     * Kiểm tra giao dịch có phải là rút tiền
     */
    public function isWithdraw()
    {
        return $this->type === self::TYPE_WITHDRAW;
    }

    /**
     * Kiểm tra giao dịch có phải là thanh toán
     */
    public function isPayment()
    {
        return $this->type === self::TYPE_PAYMENT;
    }

    /**
     * Kiểm tra giao dịch có phải là hoàn tiền
     */
    public function isRefund()
    {
        return $this->type === self::TYPE_REFUND;
    }

    /**
     * Lấy entity tham chiếu (đơn hàng, giao dịch nạp tiền, v.v.)
     */
    public function reference()
    {
        if (!$this->reference_id || !$this->reference_type) {
            return null;
        }

        // Kiểm tra xem reference_type có chứa tên đầy đủ của model không
        if (strpos($this->reference_type, '\\') === false) {
            // Nếu reference_type không chứa namespace đầy đủ, thêm namespace App\Models
            $model = 'App\\Models\\' . $this->reference_type;
        } else {
            // Nếu đã có namespace đầy đủ, sử dụng trực tiếp
            $model = $this->reference_type;
        }

        if (class_exists($model)) {
            return $model::find($this->reference_id);
        }

        // Xử lý các trường hợp đặc biệt cho tương thích ngược
        // Hỗ trợ các giá trị cũ như 'order', 'boosting_order'
        if ($this->reference_type === 'order') {
            return \App\Models\Order::find($this->reference_id);
        } elseif ($this->reference_type === 'boosting_order') {
            return \App\Models\BoostingOrder::find($this->reference_id);
        }

        return null;
    }
}
