<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'full_name',
        'role_id',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Получить роль пользователя
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Получить профиль клиента (если это клиент)
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(\Modules\Client\Models\CustomerProfile::class);
    }

    /**
     * Получить все транзакции (если продавец)
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Transaction::class, 'user_id');
    }

    /**
     * Получить все созданные купоны
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(\Modules\Seller\Models\Coupon::class, 'created_by');
    }

    /**
     * Получить все уведомления
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(\Modules\Client\Models\Notification::class);
    }

    /**
     * Получить все записи в журнале активности
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(\Modules\Client\Models\ActivityLog::class);
    }

    /**
     * Проверка: является ли владельцем
     */
    public function isOwner(): bool
    {
        return $this->role_id === Role::ROLE_OWNER;
    }

    /**
     * Проверка: является ли админом
     */
    public function isAdmin(): bool
    {
        return $this->role_id === Role::ROLE_ADMIN;
    }

    /**
     * Проверка: является ли продавцом
     */
    public function isSeller(): bool
    {
        return $this->role_id === Role::ROLE_SELLER;
    }

    /**
     * Проверка: является ли кладовщиком
     */
    public function isWarehouse(): bool
    {
        return $this->role_id === Role::ROLE_WAREHOUSE;
    }

    /**
     * Проверка: является ли клиентом
     */
    public function isClient(): bool
    {
        return $this->role_id === Role::ROLE_CLIENT;
    }

    /**
     * Проверка: имеет ли роль
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Проверка: имеет ли право доступа
     */
    public function canAccess(string $permission): bool
    {
        if (!$this->role || !$this->role->permissions) {
            return false;
        }

        return in_array($permission, $this->role->permissions);
    }

    /**
     * Получить полное имя
     */
    public function getFullNameAttribute(): string
    {
        return $this->full_name ?? $this->username;
    }

    /**
     * Получить аватар URL
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=FF6B35&background=fff';
    }
}
