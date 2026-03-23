<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Константы ролей
     */
    const ROLE_OWNER = 1;
    const ROLE_ADMIN = 2;
    const ROLE_SELLER = 3;
    const ROLE_WAREHOUSE = 4;
    const ROLE_CLIENT = 5;

    /**
     * Получить всех пользователей с этой ролью
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Проверка: является ли роль владельцем
     */
    public function isOwner(): bool
    {
        return $this->id === self::ROLE_OWNER;
    }

    /**
     * Проверка: является ли роль админом
     */
    public function isAdmin(): bool
    {
        return $this->id === self::ROLE_ADMIN;
    }

    /**
     * Проверка: является ли роль продавцом
     */
    public function isSeller(): bool
    {
        return $this->id === self::ROLE_SELLER;
    }

    /**
     * Проверка: является ли роль кладовщиком
     */
    public function isWarehouse(): bool
    {
        return $this->id === self::ROLE_WAREHOUSE;
    }

    /**
     * Проверка: является ли роль клиентом
     */
    public function isClient(): bool
    {
        return $this->id === self::ROLE_CLIENT;
    }
}
