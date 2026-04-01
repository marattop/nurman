<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Model;

class WriteoffItem extends Model
{
    protected $fillable = [
        'writeoff_id',
        'variant_id',
        'quantity',
    ];

    public function writeoff()
    {
        return $this->belongsTo(Writeoff::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
