<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'account_id',
        'order_number',
        'amount',
        'status',
        'customer_note',
        'completed_at',
        'cancelled_at',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    
    /**
     * Người dùng đặt đơn hàng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Tài khoản game được mua
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    
    /**
     * Giao dịch thanh toán của đơn hàng
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    /**
     * Tạo mã đơn hàng
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(substr(md5(time() . rand(1000, 9999)), 0, 10));
        } while (self::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }
    
    /**
     * Đánh dấu đơn hàng đã hoàn thành
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
    
    /**
     * Đánh dấu đơn hàng đã bị hủy
     */
    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'customer_note' => $reason ?: $this->customer_note,
        ]);
    }
}
