<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $fillable = ['user_id', 'birth_date', 'gender', 'address', 'city', 'bonus_points', 'total_purchases', 'purchase_count', 'last_purchase_at', 'newsletter_subscribed', 'notes'];
    protected $casts = ['birth_date' => 'date', 'bonus_points' => 'integer', 'total_purchases' => 'decimal:2', 'purchase_count' => 'integer', 'last_purchase_at' => 'datetime', 'newsletter_subscribed' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function coupons() { return $this->hasMany(Coupon::class, 'customer_id'); }

    public function getLoyaltyLevelAttribute(): string
    {
        if ($this->total_purchases >= 50000) return 'Platinum';
        if ($this->total_purchases >= 20000) return 'Gold';
        if ($this->total_purchases >= 10000) return 'Silver';
        if ($this->total_purchases >= 5000) return 'Bronze';
        return 'Standard';
    }

    public function getLoyaltyDiscountPercentAttribute(): float
    {
        return match($this->loyalty_level) {
            'Platinum' => 20, 'Gold' => 15, 'Silver' => 10, 'Bronze' => 5, default => 0,
        };
    }

    public function updateAfterPurchase(float $amount, int $bonusEarned = 0): void
    {
        $this->increment('purchase_count');
        $this->increment('total_purchases', $amount);
        $this->increment('bonus_points', $bonusEarned);
        $this->update(['last_purchase_at' => now()]);
    }
}
