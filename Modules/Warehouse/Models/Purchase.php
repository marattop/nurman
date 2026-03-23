<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'variant_id',
        'shop_id',
        'warehouse_id',
        'quantity',
        'supplier_price',
        'total_amount',
        'supplier_id',
        'received_by',
        'comment',
        'purchase_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'supplier_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    /**
     * Вариант товара
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Магазин
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Склад
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Поставщик
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'supplier_id');
    }

    /**
     * Кто принял поставку
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'received_by');
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
}
