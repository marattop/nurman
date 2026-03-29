<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель журнала событий безопасности.
 */
class SecurityLog extends Model
{
    protected $fillable = [
        'event',
        'user_id',
        'username',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'context',
        'is_critical',
    ];

    protected $casts = [
        'context' => 'array',
        'is_critical' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Связь с пользователем.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope для критических событий.
     */
    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    /**
     * Scope для событий конкретного типа.
     */
    public function scopeOfType($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope для событий конкретного пользователя.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для событий с конкретного IP.
     */
    public function scopeFromIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Получить описание события.
     */
    public function getEventDescription(): string
    {
        return match ($this->event) {
            'login.success' => 'Успешный вход в систему',
            'login.failed' => 'Неудачная попытка входа',
            'login.new_ip' => 'Вход с нового IP адреса',
            'login.new_device' => 'Вход с нового устройства',
            'logout' => 'Выход из системы',
            'password.changed' => 'Пароль изменён',
            '2fa.enabled' => 'Двухфакторная аутентификация включена',
            '2fa.disabled' => 'Двухфакторная аутентификация отключена',
            'suspicious.activity' => 'Подозрительная активность: ' . ($this->context['description'] ?? ''),
            'owner.access' => 'Доступ владельца: ' . ($this->context['action'] ?? ''),
            'admin.access' => 'Доступ администратора: ' . ($this->context['action'] ?? ''),
            'admin.product.edit' => 'Админ: редактирование товара ' . ($this->context['product_name'] ?? ''),
            'admin.product.delete' => 'Админ: удаление товара ' . ($this->context['product_name'] ?? ''),
            'admin.user.create' => 'Админ: создание пользователя ' . ($this->context['new_username'] ?? ''),
            'admin.user.delete' => 'Админ: удаление пользователя ' . ($this->context['deleted_username'] ?? ''),
            'admin.customer.coupon' => 'Админ: операция с купоном клиента',
            'admin.shop.manage' => 'Админ: управление магазином ' . ($this->context['shop_name'] ?? ''),
            'privilege.escalation' => 'Попытка повышения привилегий',
            'data.export' => 'Экспорт данных: ' . ($this->context['data_type'] ?? ''),
            default => $this->event,
        };
    }
}
