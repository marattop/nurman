<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Role;
use App\Models\ActivityLog;

/**
 * Контроллер аутентификации для клиентов (покупателей)
 * Регистрация и вход только для роли Client
 */
class ClientAuthController extends Controller
{
    /**
     * Отображение формы входа
     */
    public function showLoginForm()
    {
        if (auth()->check() && auth()->user()->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.login');
    }

    /**
     * Обработка входа клиента
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Поиск пользователя по телефону
        $user = User::where('phone', $request->phone)
            ->where('role_id', 5) // Только клиенты
            ->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Пользователь с таким номером не найден'])->onlyInput('phone');
        }

        // Проверка пароля
        if (!Hash::check($request->password, $user->password)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login_failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Неверный пароль клиента',
            ]);
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
            ->with('success', 'С возвращением, ' . $user->full_name . '!');
    }

    /**
     * Выход клиента
     */
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

    /**
     * Отображение формы регистрации
     */
    public function showRegistrationForm()
    {
        // Если уже аутентифицирован как клиент — редирект в кабинет
        if (auth()->check() && auth()->user()->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.register');
    }

    /**
     * Обработка регистрации
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Находим роль клиента
        $clientRole = Role::where('name', 'client')->first();

        if (!$clientRole) {
            return back()->withErrors(['phone' => 'Роль клиента не найдена'])->withInput();
        }

        // Создаём пользователя
        $customer = User::create([
            'username' => 'client_' . time(),
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $clientRole->id,
            'is_active' => true,
        ]);

        // Создаём профиль клиента
        CustomerProfile::create([
            'user_id' => $customer->id,
            'bonus_points' => 0,
        ]);

        // Автоматический вход после регистрации
        Auth::login($customer);

        return redirect()->route('client.dashboard')
            ->with('success', 'Добро пожаловать, ' . $customer->full_name . '!');
    }

    /**
     * Проверка телефона (AJAX)
     */
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $exists = User::where('phone', $request->phone)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Такой телефон уже зарегистрирован' : 'Телефон свободен',
        ]);
    }
}
