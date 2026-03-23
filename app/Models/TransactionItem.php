<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'variant_id', 'quantity', 'price', 'supplier_price', 'discount', 'subtotal'];
    protected $casts = ['quantity' => 'integer', 'price' => 'decimal:2', 'supplier_price' => 'decimal:2', 'discount' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function transaction() { return $this->belongsTo(Transaction::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class); }
    public function getProfitAttribute(): float { return ($this->price - $this->supplier_price) * $this->quantity - $this->discount; }
}
