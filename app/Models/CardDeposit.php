<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'metadata',
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
        'amount' => 'decimal:0',
        'actual_amount' => 'decimal:0',
        'completed_at' => 'datetime',
        'metadata' => 'json',
        'response' => 'json',
    ];
    
    /**
     * Các trạng thái nạp thẻ
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    
    /**
     * Tạo mã yêu cầu duy nhất
     * 
     * @return string
     */
    public static function generateRequestId()
    {
        return 'CARD-' . time() . rand(1000, 9999);
    }

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
        return $this->hasOne(WalletTransaction::class, 'reference_id')
            ->where('reference_type', 'card_deposit');
    }

    /**
     * Kiểm tra xem thẻ cào đã được xử lý xong chưa
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Kiểm tra xem thẻ cào đã thất bại chưa
     */
    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Kiểm tra xem thẻ cào đang chờ xử lý
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
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
    
    /**
     * Đánh dấu giao dịch là đã thất bại
     */
    public function markAsFailed()
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
        
        return $this;
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
} 