<?php

namespace Modules\Seller\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_id',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'usage_limit',
        'usage_count',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
        'comment',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Клиент, которому принадлежит купон
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'customer_id');
    }

    /**
     * Кто создал купон
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'created_by');
    }

    /**
     * Транзакции, где использован купон
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Проверка: действителен ли купон
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->usage_count >= $this->usage_limit) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Проверка: можно ли использовать с данной суммой покупки
     */
    public function canUseWithAmount(float $amount): bool
    {
        return $amount >= $this->min_purchase_amount;
    }

    /**
     * Рассчитать скидку
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->discount_type === 'wholesale') {
            // Оптовая цена - скидка равна разнице между розницей и оптом
            return 0; // Рассчитывается индивидуально для товаров
        }

        if ($this->discount_type === 'percent') {
            return min($amount * ($this->discount_value / 100), $amount);
        }

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $amount);
        }

        return 0;
    }

    /**
     * Использовать купон
     */
    public function use(): void
    {
        $this->increment('usage_count');
        
        if ($this->usage_count >= $this->usage_limit) {
            $this->update(['is_active' => false]);
        }
    }

    /**
     * Получить отображаемое название типа скидки
     */
    public function getDiscountTypeNameAttribute(): string
    {
        return match($this->discount_type) {
            'percent' => "{$this->discount_value}%",
            'fixed' => "{$this->discount_value} сом",
            'wholesale' => 'Оптовая цена',
            default => $this->discount_type,
        };
    }

    /**
     * Получить QR-код для купона
     */
    public function getQrCodeDataAttribute(): string
    {
        return json_encode([
            'type' => 'coupon',
            'code' => $this->code,
            'customer_id' => $this->customer_id,
            'discount' => $this->discount_type === 'percent' 
                ? "{$this->discount_value}%" 
                : "{$this->discount_value} сом",
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Scope: активные купоны
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: для клиента
     */
    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
