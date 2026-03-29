<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\AdminAuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Сервис безопасности для администраторов.
 *
 * Предоставляет методы для:
 * - Проверки прав доступа администратора
 * - Логирования действий
 * - Проверки rate limiting
 * - Аудита операций
 */
class AdminSecurityService
{
    /**
     * Лимиты действий (количество в минуту).
     */
    private const RATE_LIMITS = [
        'product.delete' => 10,      // Удаление товаров: 10 в минуту
        'user.create' => 5,          // Создание пользователей: 5 в минуту
        'price.change' => 20,        // Изменение цен: 20 в минуту
        'data.export' => 3,          // Экспорт данных: 3 в минуту
        'coupon.create' => 15,       // Создание купонов: 15 в минуту
        'shop.manage' => 10,         // Управление магазинами: 10 в минуту
    ];

    /**
     * Время блокировки при превышении лимита (в секундах).
     */
    private const RATE_LIMIT_DECAY = 60;

    /**
     * Проверка прав на редактирование товара.
     *
     * @param User $admin Администратор
     * @param Product $product Товар
     */
    public function canEditProduct(User $admin, Product $product): bool
    {
        // Только администраторы могут редактировать товары
        if (!$admin->isAdmin()) {
            return false;
        }

        // Товар должен существовать
        if (!$product->exists) {
            return false;
        }

        // Проверка: товар активен (или админ может редактировать неактивные)
        // Админ может редактировать любые товары
        return true;
    }

    /**
     * Проверка прав на удаление товара.
     *
     * @param User $admin Администратор
     * @param Product|null $product Товар (опционально)
     */
    public function canDeleteProduct(User $admin, ?Product $product = null): bool
    {
        // Только администраторы могут удалять товары
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'product.delete')) {
            return false;
        }

        // Если товар указан - проверяем что он существует
        if ($product && !$product->exists) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на управление пользователями.
     *
     * @param User $admin Администратор
     */
    public function canManageUsers(User $admin): bool
    {
        // Администратор может управлять пользователями
        // кроме владельцев (owner)
        if (!$admin->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на создание пользователя.
     *
     * @param User $admin Администратор
     */
    public function canCreateUser(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'user.create')) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на удаление пользователя.
     *
     * @param User $admin Администратор
     * @param User $targetUser Пользователь для удаления
     */
    public function canDeleteUser(User $admin, User $targetUser): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Админ не может удалять владельцев
        if ($targetUser->isOwner()) {
            return false;
        }

        // Админ не может удалять других администраторов (защита от конфликтов)
        // Раскомментировать если нужно ограничить
        // if ($targetUser->isAdmin()) {
        //     return false;
        // }

        return true;
    }

    /**
     * Проверка прав на управление магазинами.
     *
     * @param User $admin Администратор
     */
    public function canManageShops(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'shop.manage')) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на создание купонов клиентам.
     *
     * @param User $admin Администратор
     */
    public function canCreateCustomerCoupon(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'coupon.create')) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на изменение цен.
     *
     * @param User $admin Администратор
     */
    public function canChangePrices(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'price.change')) {
            return false;
        }

        return true;
    }

    /**
     * Проверка прав на экспорт данных.
     *
     * @param User $admin Администратор
     */
    public function canExportData(User $admin): bool
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Проверка rate limiting
        if (!$this->checkRateLimit($admin, 'data.export')) {
            return false;
        }

        return true;
    }

    /**
     * Логирование редактирования товара.
     *
     * @param User $admin Администратор
     * @param Product $product Товар
     * @param array $changes Изменения
     */
    public function logProductEdit(User $admin, Product $product, array $changes): void
    {
        AdminAuditLog::logAction(
            $admin,
            AdminAuditLog::ACTION_PRODUCT_UPDATE,
            $product,
            $changes['old'] ?? null,
            $changes['new'] ?? null
        );
    }

    /**
     * Логирование удаления товара.
     *
     * @param User $admin Администратор
     * @param Product $product Товар
     * @param bool $deactivated Товар деактивирован или удалён
     */
    public function logProductDelete(User $admin, Product $product, bool $deactivated = false): void
    {
        AdminAuditLog::logAction(
            $admin,
            AdminAuditLog::ACTION_PRODUCT_DELETE,
            $product,
            ['name' => $product->name, 'article' => $product->article, 'is_active' => $product->is_active],
            ['deleted' => true, 'deactivated' => $deactivated]
        );
    }

    /**
     * Логирование создания пользователя.
     *
     * @param User $admin Администратор
     * @param User $newUser Новый пользователь
     */
    public function logUserCreate(User $admin, User $newUser): void
    {
        AdminAuditLog::logAction(
            $admin,
            AdminAuditLog::ACTION_USER_CREATE,
            $newUser,
            null,
            [
                'username' => $newUser->username,
                'email' => $newUser->email,
                'role_id' => $newUser->role_id,
                'is_active' => $newUser->is_active,
            ]
        );
    }

    /**
     * Логирование удаления пользователя.
     *
     * @param User $admin Администратор
     * @param User $deletedUser Удалённый пользователь
     */
    public function logUserDelete(User $admin, User $deletedUser): void
    {
        AdminAuditLog::logAction(
            $admin,
            AdminAuditLog::ACTION_USER_DELETE,
            $deletedUser,
            [
                'username' => $deletedUser->username,
                'email' => $deletedUser->email,
                'role_id' => $deletedUser->role_id,
            ],
            ['deleted' => true]
        );
    }

    /**
     * Логирование создания купона.
     *
     * @param User $admin Администратор
     * @param \App\Models\Coupon $coupon Купон
     */
    public function logCouponCreate(User $admin, $coupon): void
    {
        AdminAuditLog::logAction(
            $admin,
            AdminAuditLog::ACTION_CUSTOMER_COUPON_CREATE,
            $coupon,
            null,
            [
                'code' => $coupon->code ?? null,
                'discount' => $coupon->discount ?? null,
                'customer_id' => $coupon->customer_id ?? null,
            ]
        );
    }

    /**
     * Логирование управления магазином.
     *
     * @param User $admin Администратор
     * @param \App\Models\Shop $shop Магазин
     * @param string $action Действие (create, update, delete)
     */
    public function logShopManage(User $admin, $shop, string $action): void
    {
        $auditAction = match ($action) {
            'create' => AdminAuditLog::ACTION_SHOP_CREATE,
            'update' => AdminAuditLog::ACTION_SHOP_UPDATE,
            'delete' => AdminAuditLog::ACTION_SHOP_DELETE,
            default => $action,
        };

        AdminAuditLog::logAction(
            $admin,
            $auditAction,
            $shop,
            $action === 'update' ? ['name' => $shop->name] : null,
            $action === 'create' ? ['name' => $shop->name] : null
        );
    }

    /**
     * Проверка rate limiting для действия.
     *
     * @param User $admin Администратор
     * @param string $action Тип действия
     * @return bool true если лимит не превышен
     */
    public function checkRateLimit(User $admin, string $action): bool
    {
        $limit = self::RATE_LIMITS[$action] ?? 100;
        $key = 'admin_rate_limit:' . $admin->id . ':' . $action;

        return RateLimiter::attempt(
            $key,
            $limit,
            function () {},
            self::RATE_LIMIT_DECAY
        );
    }

    /**
     * Получить оставшееся количество попыток для действия.
     *
     * @param User $admin Администратор
     * @param string $action Тип действия
     */
    public function getRateLimitRemaining(User $admin, string $action): int
    {
        $key = 'admin_rate_limit:' . $admin->id . ':' . $action;
        return RateLimiter::remaining($key, self::RATE_LIMIT_DECAY);
    }

    /**
     * Получить время сброса лимита.
     *
     * @param User $admin Администратор
     * @param string $action Тип действия
     */
    public function getRateLimitResetTime(User $admin, string $action): int
    {
        $key = 'admin_rate_limit:' . $admin->id . ':' . $action;
        return RateLimiter::availableIn($key, self::RATE_LIMIT_DECAY);
    }

    /**
     * Проверка: является ли пользователь администратором.
     *
     * @param User $user Пользователь
     */
    public function isAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Получить статистику действий администратора.
     *
     * @param User $admin Администратор
     * @param int $days Количество дней
     */
    public function getAdminStats(User $admin, int $days = 30): array
    {
        $fromDate = now()->subDays($days);

        return [
            'total_actions' => AdminAuditLog::where('user_id', $admin->id)
                ->where('created_at', '>=', $fromDate)
                ->count(),
            'products_created' => AdminAuditLog::where('user_id', $admin->id)
                ->where('action', AdminAuditLog::ACTION_PRODUCT_CREATE)
                ->where('created_at', '>=', $fromDate)
                ->count(),
            'products_updated' => AdminAuditLog::where('user_id', $admin->id)
                ->where('action', AdminAuditLog::ACTION_PRODUCT_UPDATE)
                ->where('created_at', '>=', $fromDate)
                ->count(),
            'products_deleted' => AdminAuditLog::where('user_id', $admin->id)
                ->where('action', AdminAuditLog::ACTION_PRODUCT_DELETE)
                ->where('created_at', '>=', $fromDate)
                ->count(),
            'users_created' => AdminAuditLog::where('user_id', $admin->id)
                ->where('action', AdminAuditLog::ACTION_USER_CREATE)
                ->where('created_at', '>=', $fromDate)
                ->count(),
            'users_deleted' => AdminAuditLog::where('user_id', $admin->id)
                ->where('action', AdminAuditLog::ACTION_USER_DELETE)
                ->where('created_at', '>=', $fromDate)
                ->count(),
        ];
    }
}
