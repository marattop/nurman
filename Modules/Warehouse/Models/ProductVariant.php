<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'supplier_price',
        'wholesale_price',
        'barcode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supplier_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
    ];

    /**
     * Товар, к которому относится вариант
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Остатки на складах и в магазинах
     */
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Закупки этого варианта
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * QR-коды этого варианта
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Позиции в транзакциях
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\TransactionItem::class);
    }

    /**
     * Получить общий остаток по всем складам
     */
    public function getTotalStockAttribute(): int
    {
        return $this->stock->sum('quantity');
    }

    /**
     * Получить общий зарезервированный остаток
     */
    public function getTotalReservedAttribute(): int
    {
        return $this->stock->sum('reserved');
    }

    /**
     * Получить доступный остаток (без зарезервированного)
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->total_stock - $this->total_reserved;
    }

    /**
     * Получить остаток по конкретному магазину
     */
    public function getStockByShop(int $shopId): ?Stock
    {
        return $this->stock->firstWhere('shop_id', $shopId);
    }

    /**
     * Получить остаток на складе
     */
    public function getWarehouseStock(): ?Stock
    {
        return $this->stock->firstWhereNull('shop_id');
    }

    /**
     * Проверка: есть ли в наличии
     */
    public function isInStock(): bool
    {
        return $this->available_stock > 0;
    }

    /**
     * Получить полное название с размером и цветом
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->product->name;
        
        if ($this->size) {
            $name .= " (Размер: {$this->size}";
            if ($this->color) {
                $name .= ", Цвет: {$this->color}";
            }
            $name .= ")";
        } elseif ($this->color) {
            $name .= " (Цвет: {$this->color})";
        }

        return $name;
    }

    /**
     * Получить закупочную цену
     */
    public function getCostPriceAttribute(): float
    {
        return $this->supplier_price;
    }

    /**
     * Получить розничную цену (с наценкой)
     */
    public function getRetailPriceAttribute(): float
    {
        // Если есть рекомендованная розничная цена в товаре
        if ($this->product->retail_price) {
            return $this->product->retail_price;
        }
        
        // Иначе наценка 100% от закупочной
        return $this->supplier_price * 2;
    }
}
