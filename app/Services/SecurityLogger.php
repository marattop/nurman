<?php

namespace App\Services;

use App\Models\User;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

/**
 * Сервис логирования событий безопасности.
 * 
 * Логирует:
 * - Все попытки входа (успешные/неудачные)
 * - Вход с нового IP/устройства
 * - Подозрительная активность
 * - Действия владельца в системе
 * 
 * Отправляет email уведомления при входе с нового устройства.
 */
class SecurityLogger
{
    /**
     * Типы событий безопасности.
     */
    public const EVENT_LOGIN_SUCCESS = 'login.success';
    public const EVENT_LOGIN_FAILED = 'login.failed';
    public const EVENT_LOGIN_NEW_IP = 'login.new_ip';
    public const EVENT_LOGIN_NEW_DEVICE = 'login.new_device';
    public const EVENT_LOGOUT = 'logout';
    public const EVENT_PASSWORD_CHANGED = 'password.changed';
    public const EVENT_2FA_ENABLED = '2fa.enabled';
    public const EVENT_2FA_DISABLED = '2fa.disabled';
    public const EVENT_SUSPICIOUS_ACTIVITY = 'suspicious.activity';
    public const EVENT_OWNER_ACCESS = 'owner.access';
    public const EVENT_ADMIN_ACCESS = 'admin.access';
    public const EVENT_ADMIN_PRODUCT_EDIT = 'admin.product.edit';
    public const EVENT_ADMIN_PRODUCT_DELETE = 'admin.product.delete';
    public const EVENT_ADMIN_USER_CREATE = 'admin.user.create';
    public const EVENT_ADMIN_USER_DELETE = 'admin.user.delete';
    public const EVENT_ADMIN_CUSTOMER_COUPON = 'admin.customer.coupon';
    public const EVENT_ADMIN_SHOP_MANAGE = 'admin.shop.manage';
    public const EVENT_PRIVILEGE_ESCALATION = 'privilege.escalation';
    public const EVENT_DATA_EXPORT = 'data.export';

    /**
     * Логирование события безопасности.
     *
     * @param string $event Тип события
     * @param User|null $user Пользователь
     * @param array $context Дополнительный контекст
     * @param bool $critical Критическое событие (требует email уведомления)
     */
    public function log(
        string $event,
        ?User $user = null,
        array $context = [],
        bool $critical = false
    ): SecurityLog {
        $request = request();
        
        $logData = [
            'event' => $event,
            'user_id' => $user?->id,
            'username' => $user?->username ?? $context['username'] ?? null,
            'ip_address' => $this->getIpAddress($request),
            'user_agent' => $request->userAgent(),
            'device_fingerprint' => $this->getDeviceFingerprint($request),
            'context' => $context,
            'is_critical' => $critical,
        ];

        // Сохраняем в базу данных
        $securityLog = SecurityLog::create($logData);

        // Логируем в файл
        $this->logToFile($event, $user, $context, $critical);

        // Отправляем email уведомление если критическое событие
        if ($critical && $user) {
            $this->sendNotification($user, $event, $context);
        }

        return $securityLog;
    }

    /**
     * Логирование успешного входа.
     */
    public function logLoginSuccess(User $user, Request $request): void
    {
        $context = [
            'email' => $user->email,
            'role' => $user->role?->name,
            'is_new_ip' => false,
            'is_new_device' => false,
        ];

        // Проверяем новый IP
        $knownIps = $user->known_ips ?? [];
        if (!in_array($this->getIpAddress($request), $knownIps)) {
            $context['is_new_ip'] = true;
            $this->log(self::EVENT_LOGIN_NEW_IP, $user, $context, true);
            
            // Добавляем IP к известным
            $knownIps[] = $this->getIpAddress($request);
            $user->update(['known_ips' => array_unique($knownIps)]);
        }

        // Проверяем новое устройство
        $fingerprint = $this->getDeviceFingerprint($request);
        $knownDevices = $user->known_devices ?? [];
        if (!in_array($fingerprint, $knownDevices)) {
            $context['is_new_device'] = true;
            $context['device_fingerprint'] = $fingerprint;
            $this->log(self::EVENT_LOGIN_NEW_DEVICE, $user, $context, true);
            
            // Добавляем устройство к известным
            $knownDevices[] = $fingerprint;
            $user->update(['known_devices' => array_unique($knownDevices)]);
        }

        $this->log(self::EVENT_LOGIN_SUCCESS, $user, $context);
    }

    /**
     * Логирование неудачной попытки входа.
     */
    public function logLoginFailed(string $username, string $reason = 'invalid_credentials'): void
    {
        $this->log(self::EVENT_LOGIN_FAILED, null, [
            'username' => $username,
            'reason' => $reason,
        ]);
    }

    /**
     * Логирование подозрительной активности.
     */
    public function logSuspiciousActivity(?User $user, string $description, array $context = []): void
    {
        $this->log(self::EVENT_SUSPICIOUS_ACTIVITY, $user, array_merge([
            'description' => $description,
        ], $context), true);
    }

    /**
     * Логирование доступа владельца к критическим функциям.
     */
    public function logOwnerAccess(User $user, string $action, array $context = []): void
    {
        if (!$user->isOwner()) {
            return;
        }

        $this->log(self::EVENT_OWNER_ACCESS, $user, array_merge([
            'action' => $action,
            'role' => 'owner',
        ], $context));
    }

    /**
     * Логирование доступа администратора к панели управления.
     */
    public function logAdminAccess(User $user, string $action, array $context = []): void
    {
        if (!$user->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_ACCESS, $user, array_merge([
            'action' => $action,
            'role' => 'admin',
        ], $context));
    }

    /**
     * Логирование редактирования товара администратором.
     */
    public function logAdminProductEdit(User $user, int $productId, string $productName, array $changes = []): void
    {
        if (!$user->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_PRODUCT_EDIT, $user, [
            'product_id' => $productId,
            'product_name' => $productName,
            'changes' => $changes,
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование удаления товара администратором.
     */
    public function logAdminProductDelete(User $user, int $productId, string $productName, bool $deactivated = false): void
    {
        if (!$user->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_PRODUCT_DELETE, $user, [
            'product_id' => $productId,
            'product_name' => $productName,
            'action' => $deactivated ? 'deactivated' : 'deleted',
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование создания пользователя администратором.
     */
    public function logAdminUserCreate(User $admin, int $newUserId, string $newUsername, string $role): void
    {
        if (!$admin->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_USER_CREATE, $admin, [
            'new_user_id' => $newUserId,
            'new_username' => $newUsername,
            'new_role' => $role,
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование удаления пользователя администратором.
     */
    public function logAdminUserDelete(User $admin, int $deletedUserId, string $deletedUsername, string $role): void
    {
        if (!$admin->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_USER_DELETE, $admin, [
            'deleted_user_id' => $deletedUserId,
            'deleted_username' => $deletedUsername,
            'deleted_role' => $role,
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование создания/изменения купона клиенту.
     */
    public function logAdminCustomerCoupon(User $admin, int $couponId, int $customerId, string $action): void
    {
        if (!$admin->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_CUSTOMER_COUPON, $admin, [
            'coupon_id' => $couponId,
            'customer_id' => $customerId,
            'action' => $action,
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование управления магазином.
     */
    public function logAdminShopManage(User $admin, int $shopId, string $shopName, string $action): void
    {
        if (!$admin->isAdmin()) {
            return;
        }

        $this->log(self::EVENT_ADMIN_SHOP_MANAGE, $admin, [
            'shop_id' => $shopId,
            'shop_name' => $shopName,
            'action' => $action,
            'role' => 'admin',
        ]);
    }

    /**
     * Логирование изменения пароля.
     */
    public function logPasswordChange(User $user): void
    {
        $this->log(self::EVENT_PASSWORD_CHANGED, $user, [
            'email' => $user->email,
        ], true);
    }

    /**
     * Логирование включения 2FA.
     */
    public function log2faEnabled(User $user): void
    {
        $this->log(self::EVENT_2FA_ENABLED, $user, [
            'email' => $user->email,
        ], true);
    }

    /**
     * Логирование отключения 2FA.
     */
    public function log2faDisabled(User $user): void
    {
        $this->log(self::EVENT_2FA_DISABLED, $user, [
            'email' => $user->email,
        ], true);
    }

    /**
     * Логирование экспорта данных.
     */
    public function logDataExport(User $user, string $dataType, int $recordsCount): void
    {
        $this->log(self::EVENT_DATA_EXPORT, $user, [
            'data_type' => $dataType,
            'records_count' => $recordsCount,
        ]);
    }

    /**
     * Получение IP адреса клиента.
     */
    private function getIpAddress(Request $request): string
    {
        $ip = $request->ip();
        
        $forwardedHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CF_CONNECTING_IP',
        ];

        foreach ($forwardedHeaders as $header) {
            if ($request->server->has($header)) {
                $forwardedIp = $request->server->get($header);
                $forwardedIp = explode(',', $forwardedIp)[0];
                $forwardedIp = trim($forwardedIp);
                
                if (filter_var($forwardedIp, FILTER_VALIDATE_IP)) {
                    return $forwardedIp;
                }
            }
        }

        return $ip ?? '0.0.0.0';
    }

    /**
     * Генерация отпечатка устройства.
     */
    private function getDeviceFingerprint(Request $request): string
    {
        $userAgent = $request->userAgent() ?? '';
        $ip = $this->getIpAddress($request);
        
        return hash('sha256', $userAgent . $ip . config('app.key'));
    }

    /**
     * Логирование в файл.
     */
    private function logToFile(string $event, ?User $user, array $context, bool $critical): void
    {
        $level = $critical ? 'warning' : 'info';
        
        Log::channel('security')->$level("Security Event: {$event}", [
            'user_id' => $user?->id,
            'username' => $user?->username,
            'ip' => $this->getIpAddress(request()),
            'context' => $context,
        ]);
    }

    /**
     * Отправка email уведомления.
     */
    private function sendNotification(User $user, string $event, array $context): void
    {
        // Отправляем уведомление только владельцу
        if (!$user->isOwner()) {
            return;
        }

        // Получаем email владельца
        $ownerEmail = $user->email;
        
        if (!$ownerEmail) {
            return;
        }

        try {
            // Отправляем email (реализуйте Mailable класс отдельно)
            Mail::raw($this->buildNotificationMessage($event, $context), function ($message) use ($ownerEmail, $event) {
                $message->to($ownerEmail)
                    ->subject('🔒 Безопасность NURMAN: ' . $this->getEventTitle($event));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send security notification email', [
                'error' => $e->getMessage(),
                'event' => $event,
            ]);
        }
    }

    /**
     * Построение сообщения уведомления.
     */
    private function buildNotificationMessage(string $event, array $context): string
    {
        $message = "Событие безопасности в системе NURMAN\n\n";
        $message .= "Событие: {$event}\n";
        $message .= "Время: " . now()->format('d.m.Y H:i:s') . "\n";
        $message .= "IP адрес: " . ($context['ip_address'] ?? request()->ip()) . "\n";
        
        if (!empty($context['description'])) {
            $message .= "Описание: {$context['description']}\n";
        }

        $message .= "\nЕсли это были не вы, немедленно смените пароль и обратитесь в службу безопасности.";

        return $message;
    }

    /**
     * Получение заголовка события.
     */
    private function getEventTitle(string $event): string
    {
        return match ($event) {
            self::EVENT_LOGIN_NEW_IP => 'Вход с нового IP',
            self::EVENT_LOGIN_NEW_DEVICE => 'Вход с нового устройства',
            self::EVENT_PASSWORD_CHANGED => 'Пароль изменён',
            self::EVENT_2FA_DISABLED => '2FA отключён',
            self::EVENT_SUSPICIOUS_ACTIVITY => 'Подозрительная активность',
            default => 'Событие безопасности',
        };
    }
}
