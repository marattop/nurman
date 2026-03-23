<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Encrypter;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

/**
 * Сервис двухфакторной аутентификации (2FA).
 */
class TwoFactorService
{
    /**
     * Время действия 2FA кода (5 минут).
     */
    const CODE_VALIDITY_PERIOD = 300;

    /**
     * Генерация 2FA секрета.
     */
    public function generateSecretKey()
    {
        $google2fa = new Google2FA();
        return $google2fa->generateSecretKey();
    }

    /**
     * Генерация QR кода для настройки.
     */
    public function generateQrCodeUrl($username, $secretKey, $companyName = 'NURMAN')
    {
        $google2fa = new Google2FA();
        return $google2fa->getQRCodeUrl(
            $companyName,
            $username,
            $secretKey
        );
    }

    /**
     * Включение 2FA для пользователя.
     */
    public function enableTwoFactorAuthentication(User $user)
    {
        $secretKey = $this->generateSecretKey();
        
        $user->forceFill([
            'two_factor_secret' => Encrypter::encrypt($secretKey),
            'two_factor_recovery_codes' => Encrypter::encrypt(json_encode($this->generateRecoveryCodes())),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $secretKey;
    }

    /**
     * Отключение 2FA.
     */
    public function disableTwoFactorAuthentication(User $user)
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Проверка 2FA кода.
     */
    public function verifyCode($secretKey, $code)
    {
        $google2fa = new Google2FA();
        
        try {
            return $google2fa->verifyKey($secretKey, $code, 2); // Окно ±2 периода
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Проверка кода для пользователя.
     */
    public function verifyUserCode(User $user, $code)
    {
        if (!$user->two_factor_secret) {
            return true; // 2FA не включен
        }

        $secretKey = Encrypter::decrypt($user->two_factor_secret);
        return $this->verifyCode($secretKey, $code);
    }

    /**
     * Генерация кодов восстановления.
     */
    private function generateRecoveryCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        }
        return $codes;
    }

    /**
     * Получение кодов восстановления.
     */
    public function getRecoveryCodes(User $user)
    {
        if (!$user->two_factor_recovery_codes) {
            return [];
        }

        return json_decode(Encrypter::decrypt($user->two_factor_recovery_codes), true);
    }

    /**
     * Использование кода восстановления.
     */
    public function useRecoveryCode(User $user, $code)
    {
        $codes = $this->getRecoveryCodes($user);
        
        $key = array_search($code, $codes);
        if ($key !== false) {
            unset($codes[$key]);
            
            $user->forceFill([
                'two_factor_recovery_codes' => Encrypter::encrypt(json_encode(array_values($codes))),
            ])->save();

            return true;
        }

        return false;
    }

    /**
     * Проверка необходимости 2FA.
     */
    public function needsTwoFactorVerification(User $user)
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        // Проверяем есть ли активная сессия с 2FA
        $sessionKey = '2fa_verified_' . $user->id;
        return !Cache::has($sessionKey);
    }

    /**
     * Отметка о успешной 2FA верификации.
     */
    public function markAsVerified(User $user)
    {
        $sessionKey = '2fa_verified_' . $user->id;
        Cache::put($sessionKey, true, static::CODE_VALIDITY_PERIOD);
    }

    /**
     * Генерация новых кодов восстановления.
     */
    public function regenerateRecoveryCodes(User $user)
    {
        $user->forceFill([
            'two_factor_recovery_codes' => Encrypter::encrypt(json_encode($this->generateRecoveryCodes())),
        ])->save();

        return $this->getRecoveryCodes($user);
    }
}
