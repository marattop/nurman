<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwoFactorService;
use PragmaRX\Google2FA\Google2FA;

/**
 * Контроллер для управления 2FA.
 */
class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorService $twoFactorService
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
            
            return redirect()->intended($user->getRedirectPath());
        }

        // Проверка кода восстановления
        if ($this->twoFactorService->useRecoveryCode($user, $code)) {
            $this->twoFactorService->markAsVerified($user);
            
            return redirect()->intended($user->getRedirectPath())
                ->with('success', 'Использован код восстановления. Сгенерируйте новые коды!');
        }

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
