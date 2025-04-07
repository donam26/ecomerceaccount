<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'boosting_order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'status',
        'payment_details',
        'notes',
        'completed_at',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'notes' => 'array',
        'completed_at' => 'datetime',
    ];
    
    /**
     * Đơn hàng thường liên quan đến giao dịch
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Đơn hàng cày thuê liên quan đến giao dịch
     */
    public function boostingOrder()
    {
        return $this->belongsTo(BoostingOrder::class);
    }
    
    /**
     * Kiểm tra xem giao dịch có phải cho đơn hàng cày thuê không
     */
    public function isBoostingTransaction()
    {
        return !empty($this->boosting_order_id);
    }
    
    /**
     * Đánh dấu giao dịch thành công
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Kiểm tra loại đơn hàng và cập nhật trạng thái tương ứng
        if ($this->isBoostingTransaction()) {
            // Đơn hàng cày thuê
            if ($this->boostingOrder) {
                $this->boostingOrder->status = 'paid';
                $this->boostingOrder->save();
            }
        } else {
            // Đơn hàng thường
            if ($this->order) {
                $this->order->markAsCompleted();
                
                // Cập nhật trạng thái tài khoản game
                if ($this->order->account) {
                    $this->order->account->update(['status' => 'sold']);
                }
            }
        }
    }
    
    /**
     * Đánh dấu giao dịch thất bại
     */
    public function markAsFailed($reason = null)
    {
        $data = [
            'status' => 'failed',
        ];
        
        if ($reason) {
            $paymentDetails = $this->payment_details ?: [];
            $paymentDetails['failure_reason'] = $reason;
            $data['payment_details'] = $paymentDetails;
        }
        
        $this->update($data);
    }
}
