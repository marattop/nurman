<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['expense_number', 'type', 'category', 'amount', 'shop_id', 'user_id', 'description', 'expense_date', 'receipt_photo'];
    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];

    public function shop() { return $this->belongsTo(Shop::class); }
    public function user() { return $this->belongsTo(User::class); }
}
