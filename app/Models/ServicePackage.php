<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    /**
     * Tên bảng được sử dụng bởi model.
     *
     * @var string
     */
    protected $table = 'game_service_packages';

    protected $fillable = [
        'game_service_id',
        'name',
        'image',
        'description',
        'price',
        'sale_price',
        'status',
        'display_order',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'sale_price' => 'decimal:0',
        'display_order' => 'integer',
        'metadata' => 'json',
    ];

    public function service()
    {
        return $this->belongsTo(GameService::class, 'game_service_id');
    }

    public function orders()
    {
        return $this->hasMany(ServiceOrder::class, 'game_service_package_id');
    }
    
    // Lấy giá hiển thị (có thể là giá khuyến mãi)
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price !== null && $this->sale_price > 0 
            ? $this->sale_price 
            : $this->price;
    }
}
