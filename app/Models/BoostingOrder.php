<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoostingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'service_id',
        'amount',
        'original_amount',
        'discount',
        'status',
        'game_username',
        'game_password',
        'additional_info',
        'admin_notes',
        'assigned_to',
        'completed_at'
    ];

    /**
     * Các trường ngày/giờ có thể tự động chuyển đổi
     */
    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Lấy người dùng đã đặt đơn hàng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy dịch vụ cày thuê liên quan
     */
    public function service()
    {
        return $this->belongsTo(BoostingService::class, 'service_id');
    }

    /**
     * Lấy người được giao đơn hàng (admin/nhân viên)
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Lấy các giao dịch liên quan đến đơn hàng này
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'boosting_order_id');
    }

    /**
     * Kiểm tra xem đơn hàng đã thanh toán chưa
     */
    public function isPaid()
    {
        return in_array($this->status, ['paid', 'processing', 'completed']);
    }

    /**
     * Kiểm tra xem đơn hàng đã hoàn thành chưa
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Kiểm tra xem đơn hàng đã hủy chưa
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Kiểm tra xem đơn hàng đã có thông tin tài khoản chưa
     */
    public function hasAccountInfo()
    {
        return !empty($this->game_username) && !empty($this->game_password);
    }
}
