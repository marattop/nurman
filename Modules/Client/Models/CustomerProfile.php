<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birth_date',
        'gender',
        'address',
        'city',
        'bonus_points',
        'total_purchases',
        'purchase_count',
        'last_purchase_at',
        'newsletter_subscribed',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'bonus_points' => 'integer',
        'total_purchases' => 'decimal:2',
        'purchase_count' => 'integer',
        'last_purchase_at' => 'datetime',
        'newsletter_subscribed' => 'boolean',
    ];

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class);
    }

    /**
     * История покупок
     */
    public function purchaseHistory(): HasMany
    {
        return $this->hasMany(CustomerPurchaseHistory::class, 'customer_id');
    }

    /**
     * Купоны клиента
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Coupon::class, 'customer_id');
    }

    /**
     * Уведомления
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Транзакции клиента
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Transaction::class, 'customer_id');
    }

    /**
     * Получить активный купон
     */
    public function getActiveCouponAttribute(): ?\Modules\Seller\Models\Coupon
    {
        return $this->coupons->firstWhere('is_active', true);
    }

    /**
     * Начислить бонусные баллы
     */
    public function addBonusPoints(int $points): void
    {
        $this->increment('bonus_points', $points);
    }

    /**
     * Списать бонусные баллы
     */
    public function spendBonusPoints(int $points): bool
    {
        if ($this->bonus_points < $points) {
            return false;
        }

        $this->decrement('bonus_points', $points);
        return true;
    }

    /**
     * Обновить статистику после покупки
     */
    public function updateAfterPurchase(float $amount, int $bonusEarned = 0): void
    {
        $this->increment('purchase_count');
        $this->increment('total_purchases', $amount);
        $this->increment('bonus_points', $bonusEarned);
        $this->update(['last_purchase_at' => now()]);
    }

    /**
     * Получить уровень лояльности
     */
    public function getLoyaltyLevelAttribute(): string
    {
        if ($this->total_purchases >= 50000) {
            return 'Platinum';
        } elseif ($this->total_purchases >= 20000) {
            return 'Gold';
        } elseif ($this->total_purchases >= 10000) {
            return 'Silver';
        } elseif ($this->total_purchases >= 5000) {
            return 'Bronze';
        }
        
        return 'Standard';
    }

    /**
     * Получить процент скидки по уровню лояльности
     */
    public function getLoyaltyDiscountPercentAttribute(): float
    {
        return match($this->loyalty_level) {
            'Platinum' => 20,
            'Gold' => 15,
            'Silver' => 10,
            'Bronze' => 5,
            default => 0,
        };
    }

    /**
     * Получить возраст клиента
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
    }
}
