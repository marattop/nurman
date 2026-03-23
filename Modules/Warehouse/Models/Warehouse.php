<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Остатки товаров на складе
     */
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Закупки для склада
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Управляющий складом
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'manager_id');
    }

    /**
     * Получить общий остаток товаров на складе
     */
    public function getTotalProductsCountAttribute(): int
    {
        return $this->stock->sum('quantity');
    }

    /**
     * Получить количество товаров, заканчивающихся на складе
     */
    public function getLowStockCountAttribute(): int
    {
        return $this->stock->filter(fn($s) => $s->isLowStock())->count();
    }
}
