<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Модель разрешения (permission) системы NURMAN.
 * 
 * Группы разрешений:
 * - products: управление товарами
 * - sales: продажи и транзакции
 * - finance: финансы (прибыль, себестоимость)
 * - users: управление пользователями
 * - settings: настройки системы
 * - warehouse: склад и инвентаризация
 * - reports: отчёты и аналитика
 * - customers: управление клиентами
 */
class Permission extends Model
{
    /**
     * Группы разрешений.
     */
    public const GROUP_PRODUCTS = 'products';
    public const GROUP_SALES = 'sales';
    public const GROUP_FINANCE = 'finance';
    public const GROUP_USERS = 'users';
    public const GROUP_SETTINGS = 'settings';
    public const GROUP_WAREHOUSE = 'warehouse';
    public const GROUP_REPORTS = 'reports';
    public const GROUP_CUSTOMERS = 'customers';

    /**
     * Атрибуты, доступные для массового назначения.
     */
    protected $fillable = [
        'name',
        'display_name',
        'group',
        'description',
    ];

    /**
     * Связь с ролями (многие-ко-многим).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

    /**
     * Получить все разрешения по группе.
     *
     * @param string $group Группа разрешений
     */
    public static function getByGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('group', $group)->orderBy('name')->get();
    }

    /**
     * Получить все группы разрешений.
     */
    public static function getAllGroups(): array
    {
        return [
            self::GROUP_PRODUCTS => 'Товары',
            self::GROUP_SALES => 'Продажи',
            self::GROUP_FINANCE => 'Финансы',
            self::GROUP_USERS => 'Пользователи',
            self::GROUP_SETTINGS => 'Настройки',
            self::GROUP_WAREHOUSE => 'Склад',
            self::GROUP_REPORTS => 'Отчёты',
            self::GROUP_CUSTOMERS => 'Клиенты',
        ];
    }

    /**
     * Получить все разрешения сгруппированные по группам.
     */
    public static function getGroupedPermissions(): array
    {
        $groups = self::getAllGroups();
        $result = [];

        foreach ($groups as $groupKey => $groupLabel) {
            $result[$groupKey] = [
                'label' => $groupLabel,
                'permissions' => self::where('group', $groupKey)
                    ->orderBy('name')
                    ->get(['id', 'name', 'display_name', 'description'])
                    ->toArray(),
            ];
        }

        return $result;
    }
}
