<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['invoice_number', 'variant_id', 'shop_id', 'warehouse_id', 'quantity', 'supplier_price', 'total_amount', 'supplier_id', 'received_by', 'comment', 'purchase_date'];
    protected $casts = ['quantity' => 'integer', 'supplier_price' => 'decimal:2', 'total_amount' => 'decimal:2', 'purchase_date' => 'date'];

    public function variant() { return $this->belongsTo(ProductVariant::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }
}
