<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'customer_id', 'discount_type', 'discount_value', 'min_purchase_amount', 'usage_limit', 'usage_count', 'valid_from', 'valid_until', 'is_active', 'created_by', 'comment'];
    protected $casts = ['discount_value' => 'decimal:2', 'min_purchase_amount' => 'decimal:2', 'usage_limit' => 'integer', 'usage_count' => 'integer', 'valid_from' => 'datetime', 'valid_until' => 'datetime', 'is_active' => 'boolean'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function qrCode() { return $this->hasOne(QrCode::class); }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        
        // Проверяем срок действия
        $now = now();
        if ($this->valid_until && $now->gt($this->valid_until)) return false;
        if ($this->valid_from && $now->lt($this->valid_from)) return false;
        
        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->isValid()) return 0;
        if ($this->discount_type === 'percent') return min($amount * ($this->discount_value / 100), $amount);
        // Для оптовой скидки (wholesale) скидка не рассчитывается здесь,
        // т.к. она применяется через оптовую цену товара
        if ($this->discount_type === 'wholesale') return 0;
        return 0;
    }

    public function use(): void { $this->increment('usage_count'); if ($this->usage_count >= $this->usage_limit) $this->update(['is_active' => false]); }
}
