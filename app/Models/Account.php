<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'title',
        'description',
        'price',
        'original_price',
        'username',
        'password',
        'attributes',
        'images',
        'status',
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
    ];

    /**
     * Game mà tài khoản này thuộc về
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
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
        return $this->status === 'available';
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
