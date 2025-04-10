<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'balance',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Vai trò của người dùng
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Các đơn hàng của người dùng
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Kiểm tra xem người dùng có phải admin không
     */
    public function isAdmin()
    {
        return $this->role && $this->role->slug === 'admin';
    }

    /**
     * Cộng tiền vào tài khoản
     */
    public function addBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
        
        return $this->balance;
    }

    /**
     * Trừ tiền từ tài khoản
     */
    public function subtractBalance($amount)
    {
        if ($this->balance < $amount) {
            return false;
        }
        
        $this->balance -= $amount;
        $this->save();
        
        return $this->balance;
    }

    /**
     * Get the user's wallet
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the user's wallet transactions
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get or create user wallet
     * 
     * @return \App\Models\Wallet
     */
    public function getWallet()
    {
        $wallet = $this->wallet;
        
        if (!$wallet) {
            $wallet = $this->wallet()->create([
                'balance' => 0,
                'is_active' => true,
            ]);
            
            // Làm mới mối quan hệ
            $this->refresh();
        }
        
        return $wallet;
    }
}
