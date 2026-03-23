<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'code', 'address', 'phone', 'manager_id', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function stock() { return $this->hasMany(Stock::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
    public function manager() { return $this->belongsTo(User::class, 'manager_id'); }
}
