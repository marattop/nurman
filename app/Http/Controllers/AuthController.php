<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Password;
use App\Services\SecurityLogger;
use App\Http\Middleware\RateLimitAuth;

/**
 * Контроллер аутентификации системы NURMAN.
 * Безопасная аутентификация с rate limiting и защитой от brute-force.
 */
use App\Services\AdminSecurityService;

class AuthController extends Controller
{
    /**
     * Максимальное количество попыток входа в минуту.
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Время блокировки в секундах (5 минут).
     */
    const DECAY_MINUTES = 5;

    public function __construct(
        protected SecurityLogger $securityLogger,
        protected AdminSecurityService $securityService
    ) {}

    /**
     * Отображение формы входа.
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Отображение формы второго этапа защиты.
     */
    public function showVerifyForm(Request $request)
    {
        // Только если пользователь ввел пароль, но не ввел ключ
        if (!$request->session()->has('auth_step_1')) {
            return redirect()->route('login');
        }

        return view('auth.verify');
    }

    /**
     * Обработка входа в систему с rate limiting и защитой по IP/VPN.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. ЖЕСТКАЯ ПРОВЕРКА IP и VPN
        $ipCheck = $this->securityService->validateRequest($request->ip());
        if (!$ipCheck['valid']) {
            $msg = $ipCheck['reason'] === 'foreign_ip' 
                ? "Доступ запрещен из вашей страны ({$ipCheck['country']}). Разрешен только Кыргызстан."
                : "Доступ запрещен. Использование VPN или Прокси недопустимо.";

            $this->securityLogger->logSuspiciousActivity(null, $msg, ['ip' => $request->ip()]);

            return back()->withErrors(['username' => $msg])->onlyInput('username');
        }

        // Rate limiting ключ
        $throttleKey = 'login|'.$request->ip().'|'.Str::lower($request->input('username'));

        // Проверка на превышение лимита попыток
        if (RateLimiter::tooManyAttempts($throttleKey, static::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->securityLogger->logLoginFailed($request->username, 'rate_limit');

            return back()->withErrors([
                'username' => 'Слишком много попыток входа. Попробуйте через '.ceil($seconds / 60).' мин.',
            ])->onlyInput('username');
        }

        // 2. ПОПЫТКА АУТЕНТИФИКАЦИИ (ШАГ 1: ЛОГИН/ПАРОЛЬ)
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            // Проверка активности пользователя
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['username' => 'Ваша учётная запись деактивирована.'])->onlyInput('username');
            }

            // ПЕРЕХОД НА ШАГ 2: СЕКРЕТНЫЙ КЛЮЧ
            $request->session()->put('auth_step_1', true);
            $request->session()->put('auth_user_id', $user->id);

            // Очистка rate limiter после успешного пароля
            RateLimiter::clear($throttleKey);

            return redirect()->route('login.verify');
        }

        // Неверные учётные данные
        RateLimiter::hit($throttleKey, static::DECAY_MINUTES * 60);
        $this->securityLogger->logLoginFailed($request->username, 'invalid_credentials');

        return back()->withErrors([
            'username' => 'Неверное имя пользователя или пароль',
        ])->onlyInput('username');
    }

    /**
     * Обработка второго этапа защиты (Секретный ключ).
     */
    public function verify(Request $request)
    {
        if (!$request->session()->has('auth_step_1')) {
            return redirect()->route('login');
        }

        $request->validate([
            'secret_key' => 'required|string',
        ]);

        // ПРОВЕРКА КЛЮЧА (Текущий год)
        if ($this->securityService->validateSecretKey($request->secret_key)) {
            $request->session()->forget('auth_step_1');
            $request->session()->forget('auth_user_id');
            $request->session()->put('auth_verified', true);
            
            $user = Auth::user();
            $request->session()->regenerate();

            // Логирование успеха
            $this->securityLogger->logLoginSuccess($user, $request);
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

            return redirect()->intended($user->getRedirectPath());
        }

        // Неверный ключ
        $this->securityLogger->logSuspiciousActivity(Auth::user(), 'Неверный секретный ключ 2FA');
        
        return back()->withErrors(['secret_key' => 'Неверный секретный ключ. Доступ заблокирован.']);
    }

    /**
     * Выход из системы.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->securityLogger->log(\App\Services\SecurityLogger::EVENT_LOGOUT, $user, [
                'ip' => $request->ip(),
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Выход из системы',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Отображение формы восстановления пароля.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Отправка ссылки для сброса пароля.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Отображение формы сброса пароля.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Сброс пароля с проверкой силы.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // хотя бы 1 строчная буква
                'regex:/[A-Z]/',      // хотя бы 1 заглавная буква
                'regex:/[0-9]/',      // хотя бы 1 цифра
            ],
        ], [
            'password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset',
                    'description' => 'Сброс пароля',
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Проверка сессии (для AJAX).
     */
    public function checkSession()
    {
        if (!auth()->check()) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Сессия истекла',
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'role' => [
                    'id' => $user->role?->id,
                    'name' => $user->role?->name,
                    'display_name' => $user->role?->display_name,
                    'level' => $user->role?->level,
                ],
                'permissions' => $user->getAllPermissions(),
            ],
        ]);
    }

    /**
     * Получить путь для редиректа после входа.
     * Используется в других контроллерах.
     */
    protected function getRedirectPath(User $user): string
    {
        return $user->getRedirectPath();
    }
}
