<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'permissions',
    ];
    
    protected $casts = [
        'permissions' => 'array',
    ];
    
    /**
     * Người dùng có vai trò này
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Kiểm tra xem có phải admin không
     */
    public function isAdmin()
    {
        return $this->slug === 'admin';
    }
}
