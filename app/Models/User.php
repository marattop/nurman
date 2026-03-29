<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

/**
 * Модель пользователя системы NURMAN.
 * 
 * Роли:
 * - Owner (Владелец) - ID 1, level 100 - полный доступ ко всем данным
 * - Admin (Администратор) - ID 2, level 80 - управление магазином без глубокой бухгалтерии
 * - Seller (Продавец) - ID 3, level 50 - POS-терминал, продажи
 * - Warehouse (Кладовщик) - ID 4, level 20 - склад, инвентаризация
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Атрибуты, доступные для массового назначения.
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'full_name',
        'role_id',
        'shop_id',
        'can_sell_from_warehouse',
        'avatar',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'known_ips',
        'known_devices',
    ];

    /**
     * Атрибуты, скрытые при сериализации.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'can_sell_from_warehouse' => 'boolean',
        'last_login_at' => 'datetime',
        'known_ips' => 'array',
        'known_devices' => 'array',
    ];

    /**
     * Связь с ролью.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Связь с магазином.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Связь с профилем клиента.
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    /**
     * Связь с транзакциями (продажи).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Связь с купонами (созданные пользователем).
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    /**
     * Связь с купонами клиента (принадлежащие клиенту).
     */
    public function myCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'customer_id');
    }

    /**
     * Связь с уведомлениями.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Связь с журналом активности.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Связь с закупками (как получатель товара).
     */
    public function receivedPurchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'received_by');
    }

    /**
     * Связь с QR-кодами (как печатающий).
     */
    public function printedQrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class, 'printed_by');
    }

    // ========================================================================
    // Методы проверки ролей по ID
    // ========================================================================

    /**
     * Проверка: Владелец (Owner)
     */
    public function isOwner(): bool
    {
        return $this->role_id === 1;
    }

    /**
     * Проверка: Администратор (Admin)
     */
    public function isAdmin(): bool
    {
        return $this->role_id === 2;
    }

    /**
     * Проверка: Продавец (Seller)
     */
    public function isSeller(): bool
    {
        return $this->role_id === 3;
    }

    /**
     * Проверка: Кладовщик (Warehouse)
     */
    public function isWarehouse(): bool
    {
        return $this->role_id === 4;
    }

    /**
     * Проверка: Клиент (Client)
     */
    public function isClient(): bool
    {
        return $this->role_id === 5;
    }

    // ========================================================================
    // Методы проверки ролей по имени
    // ========================================================================

    /**
     * Проверка роли по имени.
     *
     * @param string $roleName Имя роли: owner, admin, seller, warehouse, client
     */
    public function hasRole(string $roleName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->name === strtolower($roleName);
    }

    /**
     * Проверка наличия хотя бы одной из ролей.
     *
     * @param array|string $roles Массив или строка с именами ролей
     */
    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка наличия всех указанных ролей (для множественных ролей).
     */
    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    // ========================================================================
    // Методы проверки разрешений (permissions)
    // ========================================================================

    /**
     * Проверка наличия разрешения у пользователя.
     *
     * @param string|array $abilities Имя разрешения или массив разрешений
     * @param mixed $arguments Аргументы для проверки
     */
    public function can($abilities, $arguments = []): bool
    {
        // Если передан массив — проверяем все
        if (is_array($abilities)) {
            foreach ($abilities as $ability) {
                if (!$this->checkPermission($ability, $arguments)) {
                    return false;
                }
            }
            return true;
        }

        return $this->checkPermission($abilities, $arguments);
    }

    /**
     * Проверка одного разрешения (внутренний метод).
     *
     * @param string $permission Имя разрешения
     * @param mixed $arguments Аргументы
     */
    private function checkPermission(string $permission, $arguments = []): bool
    {
        // Owner имеет все разрешения автоматически
        if ($this->isOwner()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permission);
    }

    /**
     * Проверка наличия хотя бы одного разрешения из списка.
     *
     * @param array $permissions Массив имён разрешений
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверка наличия всех указанных разрешений.
     *
     * @param array $permissions Массив имён разрешений
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Получить все разрешения пользователя.
     *
     * @return array Массив имён разрешений
     */
    public function getAllPermissions(): array
    {
        // Owner имеет все разрешения
        if ($this->isOwner()) {
            return Permission::all()->pluck('name')->toArray();
        }

        if (!$this->role) {
            return [];
        }

        return $this->role->getAllPermissions();
    }

    /**
     * Получить разрешения по группе.
     *
     * @param string $group Группа разрешений: products, sales, finance, users, settings, warehouse
     */
    public function getPermissionsByGroup(string $group): array
    {
        if ($this->isOwner()) {
            return Permission::where('group', $group)->get()->pluck('name')->toArray();
        }

        if (!$this->role) {
            return [];
        }

        return $this->role->getPermissionsByGroup($group);
    }

    // ========================================================================
    // Методы для доступа к финансовым данным
    // ========================================================================

    /**
     * Может ли пользователь видеть закупочные цены (себестоимость).
     * Только Owner видит себестоимость.
     */
    public function canViewSupplierPrice(): bool
    {
        return $this->isOwner();
    }

    /**
     * Может ли пользователь видеть чистую прибыль.
     * Только Owner видит чистую прибыль.
     */
    public function canViewNetProfit(): bool
    {
        return $this->isOwner();
    }

    /**
     * Может ли пользователь видеть общую выручку.
     * Owner и Admin видят выручку.
     */
    public function canViewRevenue(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    /**
     * Может ли пользователь управлять пользователями.
     */
    public function canManageUsers(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    /**
     * Может ли пользователь подтверждать возвраты.
     */
    public function canApproveReturns(): bool
    {
        return $this->isOwner() || $this->isAdmin();
    }

    // ========================================================================
    // Методы для редиректа по ролям
    // ========================================================================

    /**
     * Получить путь для редиректа после входа в зависимости от роли.
     */
    public function getRedirectPath(): string
    {
        return match (true) {
            $this->isOwner() => route('owner.dashboard'),
            $this->isAdmin() => route('admin.dashboard'),
            $this->isSeller() => route('seller.dashboard'),
            $this->isWarehouse() => route('warehouse.dashboard'),
            $this->isClient() => route('client.dashboard'),
            default => '/',
        };
    }

    /**
     * Получить имя роли для отображения.
     */
    public function getRoleDisplayName(): string
    {
        return $this->role?->display_name ?? 'Без роли';
    }

    /**
     * Получить уровень роли.
     */
    public function getRoleLevel(): int
    {
        return $this->role?->level ?? 0;
    }

    /**
     * Проверка: имеет ли пользователь роль уровня выше или равного.
     *
     * @param int $level Минимальный уровень роли
     */
    public function hasRoleLevelAtLeast(int $level): bool
    {
        return $this->getRoleLevel() >= $level;
    }

    // ========================================================================
    // Статические методы для быстрого доступа
    // ========================================================================

    /**
     * Получить всех пользователей с указанной ролью.
     */
    public static function getByRole(string $roleName): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('role', fn($q) => $q->where('name', $roleName))->get();
    }

    /**
     * Получить всех активных пользователей с указанной ролью.
     */
    public static function getActiveByRole(string $roleName): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('role', fn($q) => $q->where('name', $roleName))
            ->where('is_active', true)
            ->get();
    }

    /**
     * Получить Owner'а системы.
     */
    public static function getOwner(): ?User
    {
        return self::where('role_id', 1)->first();
    }

    /**
     * Получить всех администраторов.
     */
    public static function getAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('role_id', 2)->get();
    }

    /**
     * Получить всех продавцов.
     */
    public static function getSellers(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('role_id', 3)->get();
    }

    /**
     * Получить всех кладовщиков.
     */
    public static function getWarehouseWorkers(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('role_id', 4)->get();
    }
}
