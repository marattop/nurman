<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Сервис управления пользователями.
 */
class UserService
{
    /**
     * Валидация данных пользователя.
     */
    public function validate(array $data, $userId = null)
    {
        $rules = [
            'username' => 'required|string|unique:users,username,' . ($userId ?? 'NULL'),
            'email' => 'nullable|email|unique:users,email,' . ($userId ?? 'NULL'),
            'phone' => 'nullable|unique:users,phone,' . ($userId ?? 'NULL'),
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'full_name' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Создание пользователя.
     */
    public function create(array $data)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'full_name' => $data['full_name'] ?? null,
            'role_id' => $data['role_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->logActivity($user, 'create', "Создан пользователь {$user->username}");

        return $user;
    }

    /**
     * Обновление пользователя.
     */
    public function update(User $user, array $data)
    {
        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'full_name' => $data['full_name'] ?? null,
            'role_id' => $data['role_id'],
            'is_active' => $data['is_active'] ?? true,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $this->logActivity($user, 'update', "Обновлены данные пользователя {$user->username}");

        return $user;
    }

    /**
     * Удаление пользователя.
     */
    public function delete(User $user)
    {
        $username = $user->username;
        $user->delete();

        $this->logActivity($user, 'delete', "Удален пользователь {$username}", null);

        return true;
    }

    /**
     * Поиск пользователей с фильтрами.
     */
    public function search(array $filters = [])
    {
        $query = User::with('role');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('username', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%")
                  ->orWhere('phone', 'like', "%{$filters['search']}%")
                  ->orWhere('full_name', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Получить все роли для сотрудников.
     */
    public function getStaffRoles()
    {
        return Role::whereNotIn('name', ['client'])->get();
    }

    /**
     * Логирование действий.
     */
    private function logActivity($user, $action, $description, $modelId = null)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => "user_{$action}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
            'model_type' => User::class,
            'model_id' => $modelId ?? $user->id,
        ]);
    }
}
