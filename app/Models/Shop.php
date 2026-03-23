<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['name', 'code', 'address', 'phone', 'manager', 'latitude', 'longitude', 'open_time', 'close_time', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function stock() { return $this->hasMany(Stock::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function expenses() { return $this->hasMany(Expense::class); }
    public function users() { return $this->hasMany(User::class, 'shop_id'); }
}
