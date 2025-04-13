<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    /**
     * Tên bảng được sử dụng bởi model.
     *
     * @var string
     */
    protected $table = 'game_service_orders';

    protected $fillable = [
        'user_id',
        'game_service_id',
        'game_service_package_id',
        'order_number',
        'amount',
        'status',
        'payment_method',
        'payment_status',
        'game_username',
        'game_password',
        'game_id',
        'game_server',
        'notes',
        'account_details',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'account_details' => 'json',
        'metadata' => 'json',
        'completed_at' => 'datetime',
    ];

    // Các trạng thái đơn hàng
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Tạo mã đơn hàng duy nhất
    public static function generateOrderNumber()
    {
        return 'SRV-' . time() . rand(1000, 9999);
    }

    // Các mối quan hệ
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(GameService::class, 'game_service_id');
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'game_service_package_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'game_service_order_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Các phương thức tiện ích
    public function isPaid()
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_PROCESSING, self::STATUS_COMPLETED]);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function hasAccountInfo()
    {
        return !empty($this->game_username) && !empty($this->game_password);
    }

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = Carbon::now();
        $this->save();
        
        // Tăng số lượng hoàn thành của dịch vụ
        $this->service->incrementCompletedCount();
        
        return $this;
    }
}
