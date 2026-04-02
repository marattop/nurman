<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\User;
use Modules\Client\Models\CustomerProfile;
use Modules\Client\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client::auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Поиск пользователя по телефону
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Пользователь с таким номером не найден'])->onlyInput('phone');
        }

        // Проверка пароля
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['phone' => 'Неверный пароль'])->onlyInput('phone');
        }

        // Проверка активности
        if (!$user->is_active) {
            return back()->withErrors(['phone' => 'Ваш аккаунт деактивирован'])->onlyInput('phone');
        }

        // Вход
        Auth::login($user);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'Клиент вошёл в систему',
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'С возвращением!');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Клиент вышел из системы',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }

    public function showRegistrationForm()
    {
        return view('client::auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|unique:users,phone',
            'full_name' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'username' => 'client_' . time(),
            'phone' => $validated['phone'],
            'full_name' => $validated['full_name'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => 5, // ROLE_CLIENT
        ]);

        // Создать профиль клиента
        CustomerProfile::create([
            'user_id' => $user->id,
        ]);

        // Записать в лог
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'Новый клиент зарегистрировался',
        ]);

        // Автоматический вход
        auth()->login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Добро пожаловать в NURMAN!');
    }
}
