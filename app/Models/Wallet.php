<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:0',
        'is_active' => 'boolean',
    ];

    /**
     * Lấy người dùng sở hữu ví
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy các giao dịch trong ví
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Thêm tiền vào ví
     *
     * @param int $amount Số tiền cần thêm
     * @param string $type Loại giao dịch (deposit, refund)
     * @param string|null $description Mô tả giao dịch
     * @param string|null $referenceId ID tham chiếu
     * @param string|null $referenceType Loại tham chiếu
     * @param array|null $metadata Metadata
     * @return WalletTransaction
     */
    public function deposit($amount, $type = 'deposit', $description = null, $referenceId = null, $referenceType = null, $metadata = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->save();

        return $this->createTransaction(
            $type, 
            $amount, 
            $balanceBefore, 
            $this->balance, 
            $description, 
            $referenceId, 
            $referenceType, 
            $metadata
        );
    }

    /**
     * Trừ tiền từ ví
     *
     * @param int $amount Số tiền cần trừ
     * @param string $type Loại giao dịch (withdraw, payment)
     * @param string|null $description Mô tả giao dịch
     * @param string|null $referenceId ID tham chiếu
     * @param string|null $referenceType Loại tham chiếu
     * @param array|null $metadata Metadata
     * @return WalletTransaction|false
     */
    public function withdraw($amount, $type = 'payment', $description = null, $referenceId = null, $referenceType = null, $metadata = null)
    {
        if ($this->balance < $amount) {
            return false;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->save();

        return $this->createTransaction(
            $type, 
            -$amount, 
            $balanceBefore, 
            $this->balance, 
            $description, 
            $referenceId, 
            $referenceType, 
            $metadata
        );
    }

    /**
     * Tạo giao dịch mới
     */
    private function createTransaction($type, $amount, $balanceBefore, $balanceAfter, $description = null, $referenceId = null, $referenceType = null, $metadata = null)
    {
        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'metadata' => $metadata ? json_encode($metadata) : null,
        ]);
    }
}
