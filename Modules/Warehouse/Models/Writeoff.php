<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Writeoff extends Model
{
    protected $fillable = [
        'reason',
        'location_type',
        'shop_id',
        'warehouse_id',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(WriteoffItem::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }

    public function getLocationNameAttribute()
    {
        if ($this->location_type === 'shop' && $this->shop) {
            return $this->shop->name;
        }
        if ($this->location_type === 'warehouse' && $this->warehouse) {
            return $this->warehouse->name;
        }
        return '—';
    }

    public function getReasonNameAttribute()
    {
        $reasons = [
            'damage' => 'Брак / Повреждение',
            'loss' => 'Потеря',
            'expired' => 'Истёк срок годности',
            'other' => 'Другое',
        ];
        return $reasons[$this->reason] ?? $this->reason;
    }
}
