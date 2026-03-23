<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPurchaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'amount',
        'bonus_earned',
        'bonus_spent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_earned' => 'integer',
        'bonus_spent' => 'integer',
    ];

    /**
     * Клиент
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'customer_id');
    }

    /**
     * Транзакция
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(\Modules\Seller\Models\Transaction::class);
    }

    /**
     * Scope: за период
     */
    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope: с бонусами
     */
    public function scopeWithBonus($query)
    {
        return $query->where('bonus_earned', '>', 0);
    }
}
