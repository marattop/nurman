<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = ['return_number', 'transaction_id', 'variant_id', 'quantity', 'refund_amount', 'reason', 'comment', 'processed_by', 'status'];
    protected $casts = ['quantity' => 'integer', 'refund_amount' => 'decimal:2'];

    public function transaction() { return $this->belongsTo(Transaction::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }
}
