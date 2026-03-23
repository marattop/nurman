<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = ['variant_id', 'coupon_id', 'qr_code_path', 'qr_data', 'is_printed', 'printed_at', 'printed_by'];
    protected $casts = ['qr_data' => 'array', 'is_printed' => 'boolean', 'printed_at' => 'datetime'];

    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function printedBy() { return $this->belongsTo(User::class, 'printed_by'); }
    public function markAsPrinted(?int $userId = null): void { $this->update(['is_printed' => true, 'printed_at' => now(), 'printed_by' => $userId]); }
}
