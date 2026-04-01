<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Shop;
use App\Models\Warehouse;

class Transfer extends Model
{
    protected $fillable = [
        'from_type',
        'from_id',
        'to_type',
        'to_id',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function fromShop()
    {
        return $this->belongsTo(Shop::class, 'from_id');
    }

    public function toShop()
    {
        return $this->belongsTo(Shop::class, 'to_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(TransferItem::class);
    }

    public function getFromLocationAttribute()
    {
        if ($this->from_type === 'shop') {
            return $this->fromShop ? $this->fromShop->name : 'Магазин #' . $this->from_id;
        }
        return $this->fromWarehouse ? $this->fromWarehouse->name : 'Склад #' . $this->from_id;
    }

    public function getToLocationAttribute()
    {
        if ($this->to_type === 'shop') {
            return $this->toShop ? $this->toShop->name : 'Магазин #' . $this->to_id;
        }
        return $this->toWarehouse ? $this->toWarehouse->name : 'Склад #' . $this->to_id;
    }

    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }
}
