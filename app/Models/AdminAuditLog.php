<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Модель журнала аудита действий администраторов.
 *
 * Хранит полную историю изменений:
 * - Кто сделал изменение
 * - Какое действие выполнено
 * - Какую модель изменили
 * - Старые и новые значения
 * - IP адрес и User Agent
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class AdminAuditLog extends Model
{
    /**
     * Типы действий.
     */
    public const ACTION_PRODUCT_CREATE = 'product.create';
    public const ACTION_PRODUCT_UPDATE = 'product.update';
    public const ACTION_PRODUCT_DELETE = 'product.delete';
    public const ACTION_PRODUCT_COPY = 'product.copy';

    public const ACTION_USER_CREATE = 'user.create';
    public const ACTION_USER_UPDATE = 'user.update';
    public const ACTION_USER_DELETE = 'user.delete';

    public const ACTION_CUSTOMER_CREATE = 'customer.create';
    public const ACTION_CUSTOMER_UPDATE = 'customer.update';
    public const ACTION_CUSTOMER_DELETE = 'customer.delete';
    public const ACTION_CUSTOMER_COUPON_CREATE = 'customer.coupon.create';
    public const ACTION_CUSTOMER_COUPON_UPDATE = 'customer.coupon.update';
    public const ACTION_CUSTOMER_COUPON_DELETE = 'customer.coupon.delete';

    public const ACTION_SHOP_CREATE = 'shop.create';
    public const ACTION_SHOP_UPDATE = 'shop.update';
    public const ACTION_SHOP_DELETE = 'shop.delete';

    public const ACTION_COUPON_CREATE = 'coupon.create';
    public const ACTION_COUPON_UPDATE = 'coupon.update';
    public const ACTION_COUPON_DELETE = 'coupon.delete';

    public const ACTION_SETTINGS_UPDATE = 'settings.update';

    /**
     * Атрибуты, доступные для массового назначения.
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * Приведение типов.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с пользователем (администратором).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope для фильтрации по действию.
     */
    public function scopeAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope для фильтрации по пользователю.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope для фильтрации по модели.
     */
    public function scopeForModel(Builder $query, string $modelType, ?int $modelId = null): Builder
    {
        $query->where('model_type', $modelType);

        if ($modelId !== null) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope для фильтрации по дате.
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope для фильтрации по IP адресу.
     */
    public function scopeFromIp(Builder $query, string $ipAddress): Builder
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope для критических действий.
     */
    public function scopeCritical(Builder $query): Builder
    {
        $criticalActions = [
            self::ACTION_USER_DELETE,
            self::ACTION_PRODUCT_DELETE,
            self::ACTION_SHOP_DELETE,
            self::ACTION_CUSTOMER_DELETE,
        ];

        return $query->whereIn('action', $criticalActions);
    }

    /**
     * Получить описание действия.
     */
    public function getActionDescription(): string
    {
        return match ($this->action) {
            self::ACTION_PRODUCT_CREATE => 'Создание товара',
            self::ACTION_PRODUCT_UPDATE => 'Обновление товара',
            self::ACTION_PRODUCT_DELETE => 'Удаление товара',
            self::ACTION_PRODUCT_COPY => 'Копирование товара',

            self::ACTION_USER_CREATE => 'Создание пользователя',
            self::ACTION_USER_UPDATE => 'Обновление пользователя',
            self::ACTION_USER_DELETE => 'Удаление пользователя',

            self::ACTION_CUSTOMER_CREATE => 'Создание клиента',
            self::ACTION_CUSTOMER_UPDATE => 'Обновление клиента',
            self::ACTION_CUSTOMER_DELETE => 'Удаление клиента',
            self::ACTION_CUSTOMER_COUPON_CREATE => 'Создание купона клиенту',
            self::ACTION_CUSTOMER_COUPON_UPDATE => 'Обновление купона клиента',
            self::ACTION_CUSTOMER_COUPON_DELETE => 'Удаление купона клиента',

            self::ACTION_SHOP_CREATE => 'Создание магазина',
            self::ACTION_SHOP_UPDATE => 'Обновление магазина',
            self::ACTION_SHOP_DELETE => 'Удаление магазина',

            self::ACTION_COUPON_CREATE => 'Создание купона',
            self::ACTION_COUPON_UPDATE => 'Обновление купона',
            self::ACTION_COUPON_DELETE => 'Удаление купона',

            self::ACTION_SETTINGS_UPDATE => 'Обновление настроек',

            default => $this->action,
        };
    }

    /**
     * Получить иконку для действия.
     */
    public function getActionIcon(): string
    {
        return match ($this->action) {
            self::ACTION_PRODUCT_CREATE,
            self::ACTION_USER_CREATE,
            self::ACTION_CUSTOMER_CREATE,
            self::ACTION_SHOP_CREATE,
            self::ACTION_COUPON_CREATE => 'plus',

            self::ACTION_PRODUCT_UPDATE,
            self::ACTION_USER_UPDATE,
            self::ACTION_CUSTOMER_UPDATE,
            self::ACTION_SHOP_UPDATE,
            self::ACTION_COUPON_UPDATE => 'edit',

            self::ACTION_PRODUCT_DELETE,
            self::ACTION_USER_DELETE,
            self::ACTION_CUSTOMER_DELETE,
            self::ACTION_SHOP_DELETE,
            self::ACTION_COUPON_DELETE => 'trash',

            default => 'info',
        };
    }

    /**
     * Получить цвет для действия.
     */
    public function getActionColor(): string
    {
        return match ($this->action) {
            self::ACTION_PRODUCT_CREATE,
            self::ACTION_USER_CREATE,
            self::ACTION_CUSTOMER_CREATE,
            self::ACTION_SHOP_CREATE,
            self::ACTION_COUPON_CREATE => 'success',

            self::ACTION_PRODUCT_UPDATE,
            self::ACTION_USER_UPDATE,
            self::ACTION_CUSTOMER_UPDATE,
            self::ACTION_SHOP_UPDATE,
            self::ACTION_COUPON_UPDATE => 'warning',

            self::ACTION_PRODUCT_DELETE,
            self::ACTION_USER_DELETE,
            self::ACTION_CUSTOMER_DELETE,
            self::ACTION_SHOP_DELETE,
            self::ACTION_COUPON_DELETE => 'danger',

            default => 'info',
        };
    }

    /**
     * Получить название модели.
     */
    public function getModelName(): string
    {
        return match ($this->model_type) {
            Product::class => 'Товар',
            User::class => 'Пользователь',
            CustomerProfile::class => 'Клиент',
            Shop::class => 'Магазин',
            Coupon::class => 'Купон',
            default => $this->model_type ?? 'Неизвестно',
        };
    }

    /**
     * Получить отформатированные старые значения.
     */
    public function getFormattedOldValues(): array
    {
        if (!$this->old_values) {
            return [];
        }

        return $this->formatValues($this->old_values);
    }

    /**
     * Получить отформатированные новые значения.
     */
    public function getFormattedNewValues(): array
    {
        if (!$this->new_values) {
            return [];
        }

        return $this->formatValues($this->new_values);
    }

    /**
     * Форматирование значений для отображения.
     */
    private function formatValues(array $values): array
    {
        $formatted = [];

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $formatted[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($value)) {
                $formatted[$key] = $value ? 'Да' : 'Нет';
            } elseif ($value === null) {
                $formatted[$key] = 'NULL';
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }

    // ========================================================================
    // Статические методы для логирования
    // ========================================================================

    /**
     * Записать действие в журнал аудита.
     *
     * @param User $user Пользователь, выполнивший действие
     * @param string $action Тип действия
     * @param Model|null $model Модель, с которой выполнено действие
     * @param array|null $oldValues Старые значения
     * @param array|null $newValues Новые значения
     */
    public static function logAction(
        User $user,
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip() ?? '0.0.0.0',
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Получить действия администратора.
     *
     * @param int $adminId ID администратора
     * @param int $limit Количество записей
     */
    public static function getAdminActions(int $adminId, int $limit = 50): \Illuminate\Support\Collection
    {
        return self::with('user')
            ->where('user_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить изменения товара.
     *
     * @param int $productId ID товара
     * @param int $limit Количество записей
     */
    public static function getProductChanges(int $productId, int $limit = 20): \Illuminate\Support\Collection
    {
        return self::with('user')
            ->where('model_type', Product::class)
            ->where('model_id', $productId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить все действия с товарами.
     */
    public static function getAllProductActions(int $limit = 100): \Illuminate\Support\Collection
    {
        return self::with(['user', 'user.role'])
            ->where('model_type', Product::class)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Получить все действия с пользователями.
     */
    public static function getAllUserActions(int $limit = 100): \Illuminate\Support\Collection
    {
        return self::with(['user', 'user.role'])
            ->where('model_type', User::class)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
