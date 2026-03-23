<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Modules\Client\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        return view('core::auth.login');
    }

    /**
     * Обработка входа
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Попытка аутентификации
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Записать в лог
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Пользователь вошел в систему',
            ]);

            // Обновить last_login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Редирект по роли
            return redirect()->intended($this->getRedirectPath($user));
        }

        return back()->withErrors([
            'username' => 'Неверное имя пользователя или пароль',
        ])->onlyInput('username');
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Записать в лог
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Пользователь вышел из системы',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Получить путь для редиректа по роли
     */
    private function getRedirectPath(User $user): string
    {
        if ($user->isOwner() || $user->isAdmin()) {
            return route('owner.dashboard');
        }

        if ($user->isSeller()) {
            return route('seller.dashboard');
        }

        if ($user->isWarehouse()) {
            return route('warehouse.dashboard');
        }

        if ($user->isClient()) {
            return route('client.dashboard');
        }

        return '/';
    }
}
