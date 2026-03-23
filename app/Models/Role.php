<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Модель роли системы NURMAN.
 * 
 * Уровни ролей (level):
 * - 100: Owner (Владелец) - полный доступ
 * - 80:  Admin (Администратор) - управление магазином
 * - 50:  Seller (Продавец) - POS-терминал
 * - 20:  Warehouse (Кладовщик) - склад
 * - 10:  Client (Клиент) - профиль покупателя
 */
class Role extends Model
{
    /**
     * Уровни ролей.
     */
    public const LEVEL_OWNER = 100;
    public const LEVEL_ADMIN = 80;
    public const LEVEL_SELLER = 50;
    public const LEVEL_WAREHOUSE = 20;
    public const LEVEL_CLIENT = 10;

    /**
     * Названия ролей.
     */
    public const NAME_OWNER = 'owner';
    public const NAME_ADMIN = 'admin';
    public const NAME_SELLER = 'seller';
    public const NAME_WAREHOUSE = 'warehouse';
    public const NAME_CLIENT = 'client';

    /**
     * Атрибуты, доступные для массового назначения.
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
        'level',
        'parent_id',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Связь с пользователями.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Связь с родительской ролью.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    /**
     * Связь с дочерними ролями.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    /**
     * Связь с разрешениями (многие-ко-многим).
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    // ========================================================================
    // Методы проверки ролей по ID
    // ========================================================================

    public function isOwner(): bool
    {
        return $this->id === 1;
    }

    public function isAdmin(): bool
    {
        return $this->id === 2;
    }

    public function isSeller(): bool
    {
        return $this->id === 3;
    }

    public function isWarehouse(): bool
    {
        return $this->id === 4;
    }

    public function isClient(): bool
    {
        return $this->id === 5;
    }

    // ========================================================================
    // Методы проверки разрешений
    // ========================================================================

    /**
     * Проверка наличия разрешения у роли.
     *
     * @param string $permissionName Имя разрешения
     */
    public function hasPermission(string $permissionName): bool
    {
        // Проверяем в кэше permissions массива
        if (is_array($this->permissions) && in_array($permissionName, $this->permissions)) {
            return true;
        }

        // Проверяем через связь с таблицей permissions
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            return false;
        }

        return $this->permissions()->where('permissions.id', $permission->id)->exists();
    }

    /**
     * Проверка наличия хотя бы одного разрешения.
     *
     * @param array $permissionNames Массив имён разрешений
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        foreach ($permissionNames as $permissionName) {
            if ($this->hasPermission($permissionName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверка наличия всех указанных разрешений.
     *
     * @param array $permissionNames Массив имён разрешений
     */
    public function hasAllPermissions(array $permissionNames): bool
    {
        foreach ($permissionNames as $permissionName) {
            if (!$this->hasPermission($permissionName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Получить все разрешения роли.
     *
     * @return array Массив имён разрешений
     */
    public function getAllPermissions(): array
    {
        // Если permissions уже загружены в массиве
        if (is_array($this->permissions) && !empty($this->permissions)) {
            return $this->permissions;
        }

        // Загружаем из БД
        return $this->permissions()->pluck('name')->toArray();
    }

    /**
     * Получить разрешения по группе.
     *
     * @param string $group Группа: products, sales, finance, users, settings, warehouse
     */
    public function getPermissionsByGroup(string $group): array
    {
        return $this->permissions()
            ->where('group', $group)
            ->pluck('name')
            ->toArray();
    }

    /**
     * Добавить разрешение роли.
     *
     * @param string $permissionName Имя разрешения
     */
    public function addPermission(string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            return false;
        }

        if (!$this->permissions()->where('permissions.id', $permission->id)->exists()) {
            $this->permissions()->attach($permission);
        }

        return true;
    }

    /**
     * Удалить разрешение у роли.
     *
     * @param string $permissionName Имя разрешения
     */
    public function removePermission(string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            return false;
        }

        $this->permissions()->detach($permission);
        return true;
    }

    /**
     * Синхронизировать разрешения роли.
     *
     * @param array $permissionNames Массив имён разрешений
     */
    public function syncPermissions(array $permissionNames): void
    {
        $permissions = Permission::whereIn('name', $permissionNames)->pluck('id');
        $this->permissions()->sync($permissions);
    }

    // ========================================================================
    // Статические методы для получения ролей
    // ========================================================================

    /**
     * Получить роль по имени.
     */
    public static function getByName(string $name): ?Role
    {
        return self::where('name', strtolower($name))->first();
    }

    /**
     * Получить роль Owner.
     */
    public static function getOwnerRole(): ?Role
    {
        return self::where('name', self::NAME_OWNER)->first();
    }

    /**
     * Получить роль Admin.
     */
    public static function getAdminRole(): ?Role
    {
        return self::where('name', self::NAME_ADMIN)->first();
    }

    /**
     * Получить роль Seller.
     */
    public static function getSellerRole(): ?Role
    {
        return self::where('name', self::NAME_SELLER)->first();
    }

    /**
     * Получить роль Warehouse.
     */
    public static function getWarehouseRole(): ?Role
    {
        return self::where('name', self::NAME_WAREHOUSE)->first();
    }

    /**
     * Получить роль Client.
     */
    public static function getClientRole(): ?Role
    {
        return self::where('name', self::NAME_CLIENT)->first();
    }

    /**
     * Получить все активные роли.
     */
    public static function getActiveRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)->orderBy('level', 'desc')->get();
    }

    /**
     * Получить все роли для управления персоналом (исключая Client).
     */
    public static function getStaffRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
            ->whereNotIn('name', [self::NAME_CLIENT])
            ->orderBy('level', 'desc')
            ->get();
    }
}
