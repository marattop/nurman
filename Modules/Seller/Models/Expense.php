<?php

namespace Modules\Seller\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'type',
        'category',
        'amount',
        'shop_id',
        'user_id',
        'description',
        'expense_date',
        'receipt_photo',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Магазин
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(\Modules\Warehouse\Models\Shop::class);
    }

    /**
     * Пользователь, создавший расход
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class);
    }

    /**
     * Получить иконку типа расхода
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'salary' => 'bi-cash',
            'rent' => 'bi-house',
            'utilities' => 'bi-lightning-charge',
            'tax' => 'bi-receipt',
            'office' => 'bi-box',
            'marketing' => 'bi-megaphone',
            'other' => 'bi-three-dots',
            default => 'bi-question-circle',
        };
    }

    /**
     * Получить отображаемое название типа
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'salary' => 'Зарплата',
            'rent' => 'Аренда',
            'utilities' => 'Коммуналка',
            'tax' => 'Налоги',
            'office' => 'Офис',
            'marketing' => 'Маркетинг',
            'other' => 'Прочее',
            default => $this->type,
        };
    }

    /**
     * Scope: по типу
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: по магазину
     */
    public function scopeForShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Scope: за период
     */
    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }
}
