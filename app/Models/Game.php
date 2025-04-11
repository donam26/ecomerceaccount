<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'banner_image',
        'is_active',
        'display_order',
    ];

    /**
     * Lấy danh sách tài khoản thuộc game này
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Lấy danh sách tài khoản có sẵn
     */
    public function availableAccounts()
    {
        return $this->accounts()->where('status', 'available');
    }

    /**
     * Lấy các dịch vụ cày thuê thuộc về game này
     */
    public function boostingServices()
    {
        return $this->hasMany(BoostingService::class);
    }

    /**
     * Lấy các dịch vụ nạp thuê thuộc về game này
     */
    public function topUpServices()
    {
        return $this->hasMany(TopUpService::class);
    }
}
