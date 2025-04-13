<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUpService extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'description',
        'price',
        'sale_price',
        'estimated_minutes',
        'thumbnail',
        'banner',
        'slug',
        'is_active',
        'login_type'
    ];

    /**
     * Lấy game liên quan đến dịch vụ nạp thuê
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Lấy tất cả đơn hàng nạp thuê của dịch vụ này
     */
    public function orders()
    {
        return $this->hasMany(TopUpOrder::class, 'service_id');
    }

    /**
     * Kiểm tra xem dịch vụ có giảm giá không
     */
    public function hasDiscount()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    /**
     * Lấy giá hiển thị (giá khuyến mãi nếu có, nếu không thì giá gốc)
     */
    public function getDisplayPrice()
    {
        return $this->hasDiscount() ? $this->sale_price : $this->price;
    }

    /**
     * Lấy phần trăm giảm giá
     */
    public function getDiscountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }
}
