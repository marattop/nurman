<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffActivityLogController extends Controller
{
    /**
     * Журнал действий персонала (кладовщики и продавцы)
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user'])
            ->whereIn('action', [
                'login',
                'logout',
                'purchase_created',
                'purchase_deleted',
                'inventory_adjustment',
                'stock_transfer',
                'sale_created',
                'sale_return',
            ])
            ->orderBy('created_at', 'desc');

        // Фильтры
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($role = $request->get('role')) {
            $query->whereHas('user', function ($q) use ($role) {
                $q->where('role_id', $role);
            });
        }

        $logs = $query->paginate(50);

        // Для фильтров
        $users = User::whereIn('role_id', [2, 3, 4]) // admin, seller, warehouse
            ->orderBy('username')
            ->get();
        
        $roles = \App\Models\Role::whereIn('id', [2, 3, 4])->get();

        $actions = [
            'login' => 'Вход в систему',
            'logout' => 'Выход из системы',
            'purchase_created' => 'Создание закупки',
            'purchase_deleted' => 'Удаление закупки',
            'inventory_adjustment' => 'Инвентаризация',
            'stock_transfer' => 'Перемещение между складами',
            'sale_created' => 'Продажа',
            'sale_return' => 'Возврат продажи',
        ];

        return view('admin.staff-logs.index', compact('logs', 'users', 'roles', 'actions'));
    }

    /**
     * Детали действия
     */
    public function show(ActivityLog $log)
    {
        return response()->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'user' => $log->user?->username ?? '—',
                'action' => $log->action,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at->format('d.m.Y H:i:s'),
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
            ]
        ]);
    }

    /**
     * Очистка журнала
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'confirm' => 'required|in:yes',
        ]);

        $query = ActivityLog::whereIn('action', [
            'login',
            'logout',
            'purchase_created',
            'purchase_deleted',
            'inventory_adjustment',
            'stock_transfer',
            'sale_created',
            'sale_return',
        ]);

        if ($validated['user_id']) {
            $query->where('user_id', $validated['user_id']);
        }

        if ($validated['action']) {
            $query->where('action', $validated['action']);
        }

        if ($validated['date_from']) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if ($validated['date_to']) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $deletedCount = $query->count();
        $query->delete();

        // Логируем очистку
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'logs_cleared',
            'description' => "Очищено {$deletedCount} записей журнала действий персонала",
            'old_values' => $validated,
        ]);

        return back()->with('success', "Очищено {$deletedCount} записей");
    }

    /**
     * Экспорт журнала в CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with(['user'])
            ->whereIn('action', [
                'login',
                'logout',
                'purchase_created',
                'purchase_deleted',
                'inventory_adjustment',
                'stock_transfer',
                'sale_created',
                'sale_return',
            ])
            ->orderBy('created_at', 'desc');

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->get();

        $csv = "ID;Дата;Пользователь;Роль;Действие;Описание;IP\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                "%s;%s;%s;%s;%s;%s;%s\n",
                $log->id,
                $log->created_at->format('d.m.Y H:i:s'),
                $log->user?->username ?? '—',
                $log->user?->role?->name ?? '—',
                $log->action,
                str_replace(';', ',', $log->description ?? ''),
                $log->ip_address ?? '—'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="staff_activity_' . date('Y-m-d') . '.csv"');
    }
}
