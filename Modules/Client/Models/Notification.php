<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Пользователь
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class);
    }

    /**
     * Получить иконку по типу
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'coupon' => 'bi-ticket',
            'sale' => 'bi-bag',
            'birthday' => 'bi-gift',
            'general' => 'bi-bell',
            default => 'bi-question-circle',
        };
    }

    /**
     * Получить цвет по типу
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'coupon' => 'primary',
            'sale' => 'success',
            'birthday' => 'warning',
            'general' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Отметить как прочитанное
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope: непрочитанные
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: по типу
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
