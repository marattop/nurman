<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_number', 'type', 'total_amount', 'discount_amount', 'payment_method', 'shop_id', 'user_id', 'customer_id', 'coupon_id', 'comment', 'meta'];
    protected $casts = ['total_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'meta' => 'array'];

    public function items() { return $this->hasMany(TransactionItem::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function returns() { return $this->hasMany(ReturnItem::class); }

    public function scopeSales($query) { return $query->where('type', 'sale'); }
    public function getProfitAttribute(): float
    {
        $profit = 0; foreach ($this->items as $item) $profit += $item->profit;
        return $profit;
    }
    public function getItemsCountAttribute(): int { return $this->items->sum('quantity'); }
}
