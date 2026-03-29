<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use App\Services\SecurityLogger;
use PragmaRX\Google2FA\Google2FA;

/**
 * Контроллер для управления 2FA.
 */
class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorService $twoFactorService,
        protected SecurityLogger $securityLogger
    ) {}

    /**
     * Страница ввода 2FA кода.
     */
    public function show()
    {
        return view('auth.2fa');
    }

    /**
     * Проверка 2FA кода.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        $code = $request->code;

        // Проверка кода
        if ($this->twoFactorService->verifyUserCode($user, $code)) {
            $this->twoFactorService->markAsVerified($user);
            
            // Устанавливаем сессию для middleware
            $sessionKey = '2fa_verified_' . $user->id;
            session([$sessionKey => true]);
            
            // Логирование успешной 2FA верификации
            $this->securityLogger->log(\App\Services\SecurityLogger::EVENT_2FA_ENABLED, $user, [
                'event' => '2fa_verified',
                'ip' => $request->ip(),
            ]);

            return redirect()->intended($user->getRedirectPath());
        }

        // Проверка кода восстановления
        if ($this->twoFactorService->useRecoveryCode($user, $code)) {
            $this->twoFactorService->markAsVerified($user);
            
            // Устанавливаем сессию для middleware
            $sessionKey = '2fa_verified_' . $user->id;
            session([$sessionKey => true]);
            
            // Логирование использования кода восстановления
            $this->securityLogger->logSuspiciousActivity($user, 'Использован код восстановления 2FA', [
                'ip' => $request->ip(),
            ]);

            return redirect()->intended($user->getRedirectPath())
                ->with('success', 'Использован код восстановления. Сгенерируйте новые коды!');
        }

        // Логирование неудачной попытки 2FA
        $this->securityLogger->logSuspiciousActivity($user, 'Неудачная попытка 2FA верификации', [
            'ip' => $request->ip(),
        ]);

        return back()->withErrors([
            'code' => 'Неверный код. Попробуйте ещё раз.',
        ]);
    }

    /**
     * Настройка 2FA.
     */
    public function enable()
    {
        $user = request()->user();

        if ($user->two_factor_secret) {
            return back()->with('error', '2FA уже включен');
        }

        $secretKey = $this->twoFactorService->enableTwoFactorAuthentication($user);
        $qrCodeUrl = $this->twoFactorService->generateQrCodeUrl($user->email ?? $user->username, $secretKey);
        $recoveryCodes = $this->twoFactorService->getRecoveryCodes($user);

        return view('auth.2fa-setup', compact('qrCodeUrl', 'recoveryCodes'));
    }

    /**
     * Отключение 2FA.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$this->twoFactorService->verifyUserCode($user, $request->code)) {
            return back()->withErrors(['code' => 'Неверный код']);
        }

        $this->twoFactorService->disableTwoFactorAuthentication($user);
        
        // Логирование отключения 2FA
        $this->securityLogger->log2faDisabled($user);

        return back()->with('success', '2FA отключен');
    }

    /**
     * Пересоздание кодов восстановления.
     */
    public function regenerateCodes(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();
        
        if (!$this->twoFactorService->verifyUserCode($user, $request->code)) {
            return back()->withErrors(['code' => 'Неверный код']);
        }

        $recoveryCodes = $this->twoFactorService->regenerateRecoveryCodes($user);

        return back()->with('success', 'Коды восстановления обновлены')
            ->with('recovery_codes', $recoveryCodes);
    }
}
