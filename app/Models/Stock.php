<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock'; // Явно указываем имя таблицы

    protected $fillable = ['variant_id', 'shop_id', 'warehouse_id', 'quantity', 'reserved', 'min_stock'];
    protected $casts = ['quantity' => 'integer', 'reserved' => 'integer', 'min_stock' => 'integer'];

    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function getAvailableAttribute(): int { return $this->quantity - $this->reserved; }
    public function isLowStock(): bool { return $this->available <= $this->min_stock; }
    public function addQuantity(int $qty): void { $this->quantity += $qty; $this->save(); }
    public function removeQuantity(int $qty): bool
    {
        if ($this->available < $qty) return false;
        $this->quantity -= $qty; $this->save(); return true;
    }
}
