<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'size', 'color', 'sku', 'supplier_price', 'wholesale_price', 'barcode', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'supplier_price' => 'decimal:2', 'wholesale_price' => 'decimal:2'];

    public function product() { return $this->belongsTo(Product::class); }
    public function stock() { return $this->hasMany(Stock::class, 'variant_id'); }
    public function purchases() { return $this->hasMany(Purchase::class); }
    public function qrCodes() { return $this->hasMany(QrCode::class, 'variant_id'); }
    public function transactionItems() { return $this->hasMany(TransactionItem::class); }

    public function getTotalStockAttribute(): int { return $this->stock->sum('quantity'); }
    public function getAvailableStockAttribute(): int { return $this->total_stock - $this->stock->sum('reserved'); }
    public function getFullNameAttribute(): string
    {
        $name = $this->product->name;
        if ($this->size) $name .= " (Размер: {$this->size}";
        if ($this->color) $name .= ($this->size ? ", Цвет: {$this->color})" : " (Цвет: {$this->color})");
        return $name;
    }
}
