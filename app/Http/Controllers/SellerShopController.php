<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер для привязки сотрудников к магазинам
 */
class SellerShopController extends Controller
{
    /**
     * Список сотрудников для привязки к магазинам
     */
    public function index()
    {
        // Получаем всех сотрудников (роли 2, 3, 4: Admin, Seller, Warehouse)
        $employees = User::with(['shop', 'role'])
            ->whereIn('role_id', [2, 3, 4])
            ->orderBy('role_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $shops = Shop::where('is_active', true)->get();

        return view('admin.sellers.shops', compact('employees', 'shops'));
    }

    /**
     * Привязка сотрудника к магазину
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $employee = User::find($validated['employee_id']);
        
        // Проверяем, что это сотрудник (роль 2, 3, 4)
        if (!in_array($employee->role_id, [2, 3, 4])) {
            return back()->with('error', 'Пользователь не является сотрудником');
        }

        $employee->update([
            'shop_id' => $validated['shop_id'] ?? null,
        ]);

        return back()->with('success', 'Сотрудник привязан к магазину');
    }

    /**
     * Отвязка сотрудника от магазина
     */
    public function detach($employeeId)
    {
        $employee = User::find($employeeId);
        
        // Проверяем, что это сотрудник (роль 2, 3, 4)
        if (!in_array($employee->role_id, [2, 3, 4])) {
            return back()->with('error', 'Пользователь не является сотрудником');
        }

        $employee->update([
            'shop_id' => null,
        ]);

        return back()->with('success', 'Сотрудник отвязан от магазина');
    }
}
