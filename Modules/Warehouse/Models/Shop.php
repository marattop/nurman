<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'manager',
        'latitude',
        'longitude',
        'open_time',
        'close_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Остатки товаров в магазине
     */
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Закупки для магазина
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Транзакции (продажи) в магазине
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Transaction::class);
    }

    /**
     * Расходы магазина
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Expense::class);
    }

    /**
     * Получить координаты для карты
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [$this->latitude, $this->longitude];
        }
        return null;
    }

    /**
     * Проверка: открыт ли магазин сейчас
     */
    public function isOpenNow(): bool
    {
        if (!$this->open_time || !$this->close_time) {
            return false;
        }

        $now = now()->format('H:i');
        
        return $now >= $this->open_time && $now <= $this->close_time;
    }

    /**
     * Получить общий остаток товаров в магазине
     */
    public function getTotalProductsCountAttribute(): int
    {
        return $this->stock->sum('quantity');
    }

    /**
     * Получить количество товаров, заканчивающихся в магазине
     */
    public function getLowStockCountAttribute(): int
    {
        return $this->stock->filter(fn($s) => $s->isLowStock())->count();
    }
}
