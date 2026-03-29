<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $fillable = [
        'variant_id',
        'shop_id',
        'warehouse_id',
        'quantity',
        'reserved',
        'min_stock',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved' => 'integer',
        'min_stock' => 'integer',
    ];

    /**
     * Вариант товара
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Магазин (если это магазин)
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Склад (если это склад)
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Получить доступный остаток
     */
    public function getAvailableAttribute(): int
    {
        return $this->quantity - $this->reserved;
    }

    /**
     * Проверка: заканчивается ли товар
     */
    public function isLowStock(): bool
    {
        return $this->available <= $this->min_stock;
    }

    /**
     * Получить местоположение (название магазина или склада)
     */
    public function getLocationNameAttribute(): string
    {
        if ($this->shop) {
            return $this->shop->name;
        }
        
        if ($this->warehouse) {
            return $this->warehouse->name;
        }

        return 'Основной склад';
    }

    /**
     * Зарезервировать количество
     */
    public function reserve(int $quantity): bool
    {
        if ($this->available < $quantity) {
            return false;
        }

        $this->reserved += $quantity;
        $this->save();
        
        return true;
    }

    /**
     * Снять резерв
     */
    public function unreserve(int $quantity): void
    {
        $this->reserved = max(0, $this->reserved - $quantity);
        $this->save();
    }

    /**
     * Увеличить остаток
     */
    public function addQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
        $this->save();
    }

    /**
     * Уменьшить остаток
     */
    public function removeQuantity(int $quantity): bool
    {
        if ($this->available < $quantity) {
            return false;
        }

        $this->quantity -= $quantity;
        $this->reserved = max(0, $this->reserved - $quantity);
        $this->save();

        return true;
    }
}
