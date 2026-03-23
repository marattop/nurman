<?php

namespace Modules\Seller\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type',
        'total_amount',
        'discount_amount',
        'payment_method',
        'shop_id',
        'user_id',
        'customer_id',
        'coupon_id',
        'comment',
        'meta',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'meta' => 'array',
    ];

    /**
     * Позиции транзакции
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Магазин
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(\Modules\Warehouse\Models\Shop::class);
    }

    /**
     * Продавец
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class);
    }

    /**
     * Клиент
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'customer_id');
    }

    /**
     * Использованный купон
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Возвраты по транзакции
     */
    public function returns(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\ReturnItem::class);
    }

    /**
     * Scope: только продажи
     */
    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    /**
     * Scope: только возвраты
     */
    public function scopeReturns($query)
    {
        return $query->where('type', 'return');
    }

    /**
     * Получить общую сумму без скидки
     */
    public function getSubtotalAttribute(): float
    {
        return $this->total_amount + $this->discount_amount;
    }

    /**
     * Получить общую прибыль
     */
    public function getProfitAttribute(): float
    {
        $profit = 0;
        foreach ($this->items as $item) {
            $profit += $item->profit;
        }
        return $profit;
    }

    /**
     * Получить количество товаров в чеке
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Статус транзакции
     */
    public function getStatusAttribute(): string
    {
        if ($this->type === 'return') {
            return 'Возврат';
        }

        return 'Продажа';
    }

    /**
     * Получить иконку типа оплаты
     */
    public function getPaymentIconAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'bi-cash',
            'card' => 'bi-credit-card',
            'transfer' => 'bi-arrow-left-right',
            default => 'bi-question-circle',
        };
    }
}
