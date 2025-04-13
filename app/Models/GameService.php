<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'image',
        'status',
        'is_featured',
        'metadata',
        'completed_count',
        'game_id',
        'type'
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'sale_price' => 'decimal:0',
        'is_featured' => 'boolean',
        'metadata' => 'json',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function packages()
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function orders()
    {
        return $this->hasMany(ServiceOrder::class);
    }
    
    // Tăng số lượng hoàn thành
    public function incrementCompletedCount()
    {
        $this->increment('completed_count');
    }
    
    // Lấy giá hiển thị (có thể là giá khuyến mãi)
    public function getDisplayPriceAttribute()
    {
        return $this->sale_price !== null && $this->sale_price > 0 
            ? $this->sale_price 
            : $this->price;
    }
}
