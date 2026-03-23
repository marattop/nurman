<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\Role;

/**
 * Контроллер аутентификации для клиентов (покупателей)
 * Регистрация и вход только для роли Client
 */
class ClientAuthController extends Controller
{
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
