<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User;
use Modules\Client\Models\CustomerProfile;
use Modules\Client\Models\ActivityLog;

class AuthController extends Controller
{
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
