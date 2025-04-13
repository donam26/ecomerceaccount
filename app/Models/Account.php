<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'account_category_id',
        'title',
        'description',
        'price',
        'original_price',
        'username',
        'password',
        'attributes',
        'images',
        'status',
        'reserved_until',
        'sold_at',
        'is_featured',
        'is_verified',
    ];

    protected $casts = [
        'attributes' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'reserved_until' => 'datetime',
        'sold_at' => 'datetime',
    ];

    /**
     * Game mà tài khoản này thuộc về
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Danh mục mà tài khoản này thuộc về
     */
    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    /**
     * Các đơn hàng của tài khoản này
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Kiểm tra xem tài khoản có còn khả dụng không
     */
    public function isAvailable()
    {
        // Tài khoản có status là available là chắc chắn khả dụng
        if ($this->status === 'available') {
            return true;
        }
        
        // Nếu tài khoản ở trạng thái 'pending', kiểm tra thời gian hết hạn
        if ($this->status === 'pending' && $this->reserved_until) {
            // Sử dụng raw query để kiểm tra
            $isExpired = DB::select(
                "SELECT (reserved_until < NOW()) as is_expired 
                 FROM accounts 
                 WHERE id = ?", 
                [$this->id]
            );
            
            // Nếu hết hạn thì coi như available
            if (!empty($isExpired) && $isExpired[0]->is_expired) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Kiểm tra xem tài khoản có đang được giữ không
     */
    public function isReserved()
    {
        if ($this->status !== 'pending' || !$this->reserved_until) {
            return false;
        }
        
        // Kiểm tra xem thời gian giữ chỗ còn hiệu lực không
        $isStillReserved = DB::select(
            "SELECT (reserved_until > NOW()) as is_reserved 
             FROM accounts 
             WHERE id = ?", 
            [$this->id]
        );
        
        return !empty($isStillReserved) && $isStillReserved[0]->is_reserved;
    }

    /**
     * Giải phóng tài khoản nếu hết thời gian giữ chỗ
     */
    public function releaseIfExpired()
    {
        if ($this->status !== 'pending' || !$this->reserved_until) {
            return false;
        }
        
        // Kiểm tra và cập nhật trong một câu lệnh duy nhất
        $released = DB::update(
            "UPDATE accounts 
             SET status = 'available', reserved_until = NULL, updated_at = NOW() 
             WHERE id = ? AND status = 'pending' AND reserved_until < NOW()",
            [$this->id]
        );
        
        if ($released) {
            // Refresh model để cập nhật dữ liệu
            $this->refresh();
            return true;
        }
        
        return false;
    }

    /**
     * Tính giảm giá (nếu có)
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        
        return 0;
    }
}
