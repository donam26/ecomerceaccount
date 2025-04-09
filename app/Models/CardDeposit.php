<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardDeposit extends Model
{
    use HasFactory;

    /**
     * Các trường có thể gán hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'wallet_id',
        'telco',
        'amount',
        'serial',
        'code',
        'request_id',
        'trans_id',
        'status',
        'actual_amount',
        'response',
        'transaction_id',
        'completed_at',
    ];

    /**
     * Các trường ngày tháng
     *
     * @var array
     */
    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Casting thuộc tính
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'actual_amount' => 'float',
        'completed_at' => 'datetime',
    ];

    /**
     * Mối quan hệ với user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với ví
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Mối quan hệ với giao dịch
     */
    public function transaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'transaction_id');
    }

    /**
     * Kiểm tra xem thẻ cào đã được xử lý xong chưa
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Kiểm tra xem thẻ cào đã thất bại chưa
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Kiểm tra xem thẻ cào đang chờ xử lý
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Lấy tên nhà mạng đầy đủ
     */
    public function getTelcoNameAttribute()
    {
        $telcoMap = [
            'VIETTEL' => 'Viettel',
            'MOBIFONE' => 'Mobifone',
            'VINAPHONE' => 'Vinaphone',
            'ZING' => 'Zing',
            'GATE' => 'Gate',
            'VCOIN' => 'VCoin',
        ];

        return $telcoMap[$this->telco] ?? $this->telco;
    }

    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope để lọc theo trạng thái thất bại
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    /**
     * Scope để lọc theo trạng thái đang chờ xử lý
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
} 