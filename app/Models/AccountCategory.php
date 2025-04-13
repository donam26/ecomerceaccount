<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'display_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Mối quan hệ với tài khoản
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Lấy số lượng tài khoản có sẵn trong danh mục
     */
    public function availableAccounts()
    {
        return $this->accounts()->where('status', 'available');
    }

    /**
     * Định nghĩa các thuộc tính phụ
     */
    public function getUrlAttribute()
    {
        return route('account.category', $this->slug);
    }
} 