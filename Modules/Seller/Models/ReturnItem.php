<?php

namespace Modules\Seller\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'transaction_id',
        'variant_id',
        'quantity',
        'refund_amount',
        'reason',
        'comment',
        'processed_by',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Транзакция
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Вариант товара
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(\Modules\Warehouse\Models\ProductVariant::class);
    }

    /**
     * Кто обработал возврат
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'processed_by');
    }

    /**
     * Получить название товара
     */
    public function getProductNameAttribute(): string
    {
        return $this->variant->product->name;
    }

    /**
     * Получить статус текстом
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Ожидает',
            'approved' => 'Одобрен',
            'rejected' => 'Отклонен',
            'completed' => 'Завершен',
            default => $this->status,
        };
    }

    /**
     * Получить причину текстом
     */
    public function getReasonTextAttribute(): string
    {
        return match($this->reason) {
            'defect' => 'Брак',
            'size' => 'Не подошел размер',
            'color' => 'Не понравился цвет',
            'quality' => 'Качество',
            'other' => 'Другое',
            default => $this->reason,
        };
    }

    /**
     * Одобрить возврат
     */
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Отклонить возврат
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Завершить возврат
     */
    public function complete(): void
    {
        $this->update(['status' => 'completed']);
        
        // Вернуть товар на склад
        $stock = $this->variant->stock->firstWhere('shop_id', $this->transaction->shop_id);
        if ($stock) {
            $stock->addQuantity($this->quantity);
        }
    }
}
