<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер для управления ролями и разрешениями.
 * Доступен только для Owner (Владельца).
 */
class RoleController extends Controller
{
    /**
     * Отображение списка ролей.
     */
    public function index()
    {
        $roles = Role::withCount('users')
            ->orderBy('level', 'desc')
            ->get();

        return view('owner.roles.index', compact('roles'));
    }

    /**
     * Форма создания роли.
     */
    public function create()
    {
        $permissions = Permission::getGroupedPermissions();
        $parentRoles = Role::where('is_active', true)
            ->where('level', '>', Role::LEVEL_CLIENT)
            ->orderBy('level', 'desc')
            ->get();

        return view('owner.roles.create', compact('permissions', 'parentRoles'));
    }

    /**
     * Сохранение новой роли.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:1|max:100',
            'parent_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => strtolower($validated['name']),
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => true,
        ]);

        // Синхронизируем разрешения
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_create',
            'description' => "Создана роль '{$role->display_name}'",
            'model_type' => Role::class,
            'model_id' => $role->id,
        ]);

        return redirect()->route('owner.roles.index')
            ->with('success', "Роль '{$role->display_name}' успешно создана");
    }

    /**
     * Отображение формы редактирования роли.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::getGroupedPermissions();
        $parentRoles = Role::where('is_active', true)
            ->where('id', '!=', $role->id)
            ->where('level', '>', Role::LEVEL_CLIENT)
            ->orderBy('level', 'desc')
            ->get();

        $rolePermissions = $role->getAllPermissions();

        return view('owner.roles.edit', compact('role', 'permissions', 'parentRoles', 'rolePermissions'));
    }

    /**
     * Обновление роли.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:1|max:100',
            'parent_id' => 'nullable|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $oldDisplayName = $role->display_name;

        $role->update([
            'name' => strtolower($validated['name']),
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_update',
            'description' => "Обновлена роль '{$oldDisplayName}'",
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => ['display_name' => $oldDisplayName],
            'new_values' => ['display_name' => $role->display_name],
        ]);

        return redirect()->route('owner.roles.index')
            ->with('success', "Роль '{$role->display_name}' успешно обновлена");
    }

    /**
     * Удаление роли.
     */
    public function destroy(Role $role)
    {
        // Нельзя удалить базовые роли
        if ($role->isOwner() || $role->isAdmin() || $role->isSeller() || $role->isWarehouse()) {
            return back()->with('error', 'Нельзя удалить системную роль');
        }

        // Нельзя удалить роль, если есть пользователи с этой ролью
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Нельзя удалить роль, пока есть пользователи с этой ролью');
        }

        $roleName = $role->display_name;
        $role->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_delete',
            'description' => "Удалена роль '{$roleName}'",
            'model_type' => Role::class,
            'model_id' => $role->id,
        ]);

        return redirect()->route('owner.roles.index')
            ->with('success', "Роль '{$roleName}' успешно удалена");
    }

    /**
     * Отображение разрешений роли.
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::getGroupedPermissions();
        $rolePermissions = $role->getAllPermissions();

        return view('owner.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Синхронизация разрешений роли.
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
        ]);

        $oldPermissions = $role->getAllPermissions();
        $newPermissions = $validated['permissions'] ?? [];

        $role->syncPermissions($newPermissions);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_permissions_update',
            'description' => "Обновлены разрешения для роли '{$role->display_name}'",
            'model_type' => Role::class,
            'model_id' => $role->id,
            'old_values' => ['permissions' => $oldPermissions],
            'new_values' => ['permissions' => $newPermissions],
        ]);

        return redirect()->route('owner.roles.permissions', $role)
            ->with('success', 'Разрешения роли успешно обновлены');
    }

    /**
     * Просмотр пользователей с данной ролью.
     */
    public function users(Role $role)
    {
        $users = User::where('role_id', $role->id)
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('owner.roles.users', compact('role', 'users'));
    }

    /**
     * Дублирование роли.
     */
    public function duplicate(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . '_copy_' . time(),
            'display_name' => $role->display_name . ' (Копия)',
            'description' => $role->description,
            'level' => $role->level,
            'parent_id' => $role->parent_id,
            'is_active' => $role->is_active,
        ]);

        // Копируем разрешения
        $newRole->syncPermissions($role->getAllPermissions());

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_duplicate',
            'description' => "Создана копия роли '{$role->display_name}'",
            'model_type' => Role::class,
            'model_id' => $newRole->id,
        ]);

        return redirect()->route('owner.roles.edit', $newRole)
            ->with('success', 'Роль успешно скопирована. Измените имя роли.');
    }
}
