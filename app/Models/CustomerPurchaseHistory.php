<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPurchaseHistory extends Model
{
    protected $fillable = ['customer_id', 'transaction_id', 'amount', 'bonus_earned', 'bonus_spent'];
    protected $casts = ['amount' => 'decimal:2', 'bonus_earned' => 'integer', 'bonus_spent' => 'integer'];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function transaction() { return $this->belongsTo(Transaction::class); }
}
