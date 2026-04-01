<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    protected $fillable = [
        'transfer_id',
        'variant_id',
        'quantity',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
