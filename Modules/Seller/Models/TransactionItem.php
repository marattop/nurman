<?php

namespace Modules\Seller\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'variant_id',
        'quantity',
        'price',
        'supplier_price',
        'discount',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'supplier_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Транзакция
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Вариант товара
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(\Modules\Warehouse\Models\ProductVariant::class);
    }

    /**
     * Получить название товара
     */
    public function getProductNameAttribute(): string
    {
        return $this->variant->product->name;
    }

    /**
     * Получить размер и цвет
     */
    public function getVariantInfoAttribute(): string
    {
        $info = [];
        if ($this->variant->size) {
            $info[] = "Размер: {$this->variant->size}";
        }
        if ($this->variant->color) {
            $info[] = "Цвет: {$this->variant->color}";
        }
        return implode(', ', $info);
    }

    /**
     * Получить прибыль по позиции
     */
    public function getProfitAttribute(): float
    {
        return ($this->price - $this->supplier_price) * $this->quantity - $this->discount;
    }

    /**
     * Получить сумму до скидки
     */
    public function getTotalBeforeDiscountAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
