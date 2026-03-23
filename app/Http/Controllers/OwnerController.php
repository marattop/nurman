<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер Владельца (Owner).
 * Полный доступ ко всем функциям системы, включая финансы.
 * Оптимизирован для 100+ пользователей с кэшированием.
 */
class OwnerController extends Controller
{
    /**
     * Время кэширования дашборда (5 минут).
     */
    const CACHE_TTL = 300;

    /**
     * Дашборд владельца с кэшированием.
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'month');
        $customDate = $request->get('custom_date');

        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period, $customDate);

        // Кэш ключ
        $cacheKey = 'owner_dashboard_data_'.auth()->id().'_'.$period.'_'.md5($dateFrom.$dateTo);

        // Получаем данные из кэша
        $data = Cache::remember($cacheKey, static::CACHE_TTL, function() use ($dateFrom, $dateTo, $periodLabel, $period) {
            return $this->getDashboardData($dateFrom, $dateTo, $periodLabel, $period);
        });

        // Возвращаем представление с данными
        return view('owner::dashboard', $data);
    }

    /**
     * Получение данных дашборда.
     */
    private function getDashboardData($dateFrom, $dateTo, $periodLabel, $period)
    {
        // Выручка - оптимизированный запрос
        $revenue = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
            ->first();

        // Себестоимость - оптимизированный запрос с индексом
        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('supplier_price * quantity'));

        // Валовая прибыль
        $grossProfit = $revenue->total - $costOfGoods;
        $grossMargin = $revenue->total > 0 ? ($grossProfit / $revenue->total) * 100 : 0;

        // Закупки
        $purchases = Purchase::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->first();

        // Расходы
        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
            ->first();

        // Чистая прибыль
        $netProfit = $grossProfit - $purchases->total - $expenses->total;
        $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

        // Выручка по источникам
        $revenueBySource = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get();

        // Выручка по магазинам - оптимизировано
        $revenueByShop = DB::table('shops')
            ->leftJoin('transactions', fn($j) => $j->on('shops.id', '=', 'transactions.shop_id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('shops.name, COUNT(transactions.id) as count, COALESCE(SUM(transactions.total_amount), 0) as total')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('total', 'desc')
            ->get();

        // Топ товаров - оптимизировано с индексами
        $topProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('products.id, products.name, products.article, products.photo,
                SUM(transaction_items.quantity) as sold_qty,
                SUM(transaction_items.price * transaction_items.quantity) as revenue,
                SUM(transaction_items.supplier_price * transaction_items.quantity) as cost,
                SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity) as profit')
            ->groupBy('products.id')
            ->havingRaw('profit > 0')
            ->orderBy('profit', 'desc')
            ->limit(10)
            ->get();

        // Последние продажи
        $recentSales = Transaction::sales()
            ->with(['user', 'shop'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Статистика по сотрудникам
        $staffStats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'sellers' => User::where('role_id', 3)->count(),
            'warehouse' => User::where('role_id', 4)->count(),
        ];

        // Количество магазинов
        $totalShops = DB::table('shops')->where('is_active', 1)->count();
        
        // Количество сотрудников
        $totalEmployees = User::where('is_active', true)->count();
        
        // Расходы по категориям
        $expensesByCategory = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('type as category, SUM(amount) as total')
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();
        
        // Динамика выручки (по дням)
        $revenueDynamics = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // Динамика прибыли (по дням)
        $profitDynamics = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(transactions.created_at) as date,
                SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity) as profit')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
        
        // Рост выручки (сравнение с предыдущим периодом)
        $prevDateFrom = date('Y-m-d', strtotime($dateFrom . ' -1 month'));
        $prevDateTo = date('Y-m-d', strtotime($dateTo . ' -1 month'));
        $prevRevenue = Transaction::sales()
            ->whereBetween('created_at', [$prevDateFrom, $prevDateTo])
            ->sum('total_amount');
        $revenueGrowth = $prevRevenue > 0 ? (($revenue->total - $prevRevenue) / $prevRevenue) * 100 : 0;
        
        // Рост прибыли
        $prevProfit = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$prevDateFrom, $prevDateTo])
            ->sum(DB::raw('(transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity'));
        $profitGrowth = $prevProfit > 0 ? (($netProfit - $prevProfit) / $prevProfit) * 100 : 0;
        
        // Рост расходов
        $prevExpenses = Expense::whereBetween('created_at', [$prevDateFrom, $prevDateTo])->sum('amount');
        $expensesGrowth = $prevExpenses > 0 ? (($expenses->total - $prevExpenses) / $prevExpenses) * 100 : 0;
        
        // Добавим quantity к закупкам (если таблица существует)
        try {
            $purchases->quantity = DB::table('purchases')
                ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->whereBetween('purchases.created_at', [$dateFrom, $dateTo])
                ->sum('purchase_items.quantity');
        } catch (\Exception $e) {
            $purchases->quantity = 0;
        }

        return compact(
            'periodLabel', 'revenue', 'costOfGoods', 'grossProfit', 'grossMargin',
            'purchases', 'expenses', 'netProfit', 'netMargin',
            'revenueBySource', 'revenueByShop', 'topProducts', 'recentSales', 'staffStats',
            'totalShops', 'totalEmployees', 'expensesByCategory',
            'revenueDynamics', 'profitDynamics',
            'revenueGrowth', 'profitGrowth', 'expensesGrowth',
            'dateFrom', 'dateTo'
        );
    }

    /**
     * Финансы: детальная информация.
     */
    public function finance(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        // Детализация доходов
        $income = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Детализация расходов
        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('expense_date as date, type, SUM(amount) as amount')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        return view('owner.finance.index', compact('income', 'expenses', 'period', 'periodLabel'));
    }

    /**
     * Прибыль: отчёт.
     */
    public function profit(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $revenue = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');

        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('supplier_price * quantity'));

        $grossProfit = $revenue - $costOfGoods;

        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount');
        $purchases = Purchase::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount');

        $netProfit = $grossProfit - $expenses - $purchases;

        return view('owner.finance.profit', compact('revenue', 'costOfGoods', 'grossProfit', 'expenses', 'purchases', 'netProfit', 'periodLabel'));
    }

    /**
     * Маржинальность товаров.
     */
    public function margin(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $products = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->leftJoin('transactions', fn($j) => $j->on('transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('products.id, products.name, products.article,
                AVG(product_variants.supplier_price) as avg_supplier_price,
                AVG(transaction_items.price) as avg_retail_price,
                SUM(transaction_items.quantity) as sold_qty')
            ->groupBy('products.id')
            ->havingRaw('sold_qty > 0')
            ->orderBy('avg_retail_price', 'desc')
            ->paginate(50);

        return view('owner.finance.margin', compact('products', 'periodLabel'));
    }

    /**
     * Баланс компании.
     */
    public function balance()
    {
        // Текущие активы
        $cash = Transaction::sales()->sum('total_amount');
        $inventoryValue = DB::table('stock')
            ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
            ->sum(DB::raw('product_variants.supplier_price * stock.quantity'));

        // Обязательства
        $accountsPayable = Expense::whereNull('paid_at')->sum('amount');

        $totalAssets = $cash + $inventoryValue;
        $totalLiabilities = $accountsPayable;
        $equity = $totalAssets - $totalLiabilities;

        return view('owner.finance.balance', compact('cash', 'inventoryValue', 'accountsPayable', 'totalAssets', 'totalLiabilities', 'equity'));
    }

    /**
     * Управление пользователями: список.
     */
    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('full_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();

        return view('owner.users.index', compact('users', 'roles'));
    }

    /**
     * Форма создания пользователя.
     */
    public function createUser()
    {
        $roles = Role::getStaffRoles();
        return view('owner.users.create', compact('roles'));
    }

    /**
     * Сохранение пользователя.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|unique:users,phone',
            'password' => 'required|min:6',
            'full_name' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_create',
            'description' => "Создан пользователь {$user->username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return redirect()->route('owner.users.index')->with('success', 'Пользователь создан');
    }

    /**
     * Форма редактирования пользователя.
     */
    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('owner.users.edit', compact('user', 'roles'));
    }

    /**
     * Обновление пользователя.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:6',
            'full_name' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $data = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'full_name' => $validated['full_name'],
            'role_id' => $validated['role_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_update',
            'description' => "Обновлен пользователь {$user->username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return redirect()->route('owner.users.index')->with('success', 'Пользователь обновлён');
    }

    /**
     * Удаление пользователя.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Нельзя удалить самого себя');
        }

        $username = $user->username;
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_delete',
            'description' => "Удален пользователь {$username}",
            'model_type' => User::class,
            'model_id' => $user->id,
        ]);

        return redirect()->route('owner.users.index')->with('success', 'Пользователь удалён');
    }

    /**
     * Журнал действий (Activity Logs).
     */
    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::all();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('owner.logs.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Экспорт логов.
     */
    public function exportLogs(Request $request)
    {
        // Реализация экспорта в CSV
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(1000)
            ->get();

        $csv = "ID,Пользователь,Действие,Описание,IP,Дата\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->user?->username ?? 'N/A',
                $log->action,
                str_replace('"', '""', $log->description ?? ''),
                $log->ip_address ?? '',
                $log->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"');
    }

    /**
     * Отчёты: главная страница.
     */
    public function reports()
    {
        return view('owner.reports.index');
    }

    /**
     * Отчёт по продажам.
     */
    public function salesReport(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $sales = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'shop', 'items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->total();

        return view('owner.reports.sales', compact('sales', 'totalSales', 'totalTransactions', 'periodLabel'));
    }

    /**
     * Отчёт по товарам.
     */
    public function productsReport(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $products = DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->leftJoin('transactions', fn($j) => $j->on('transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('products.id, products.name, products.article, products.category_id,
                COUNT(DISTINCT product_variants.id) as variants_count,
                COALESCE(SUM(transaction_items.quantity), 0) as sold_qty,
                COALESCE(SUM(transaction_items.price * transaction_items.quantity), 0) as revenue')
            ->groupBy('products.id')
            ->orderBy('sold_qty', 'desc')
            ->paginate(50);

        return view('owner.reports.products', compact('products', 'periodLabel'));
    }

    /**
     * Финансовый отчёт.
     */
    public function financeReport(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $revenue = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount');

        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('supplier_price * quantity'));

        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('type, SUM(amount) as amount')
            ->groupBy('type')
            ->get();

        $profit = $revenue - $costOfGoods;
        foreach ($expenses as $expense) {
            $profit -= $expense->amount;
        }

        return view('owner.reports.finance', compact('revenue', 'costOfGoods', 'expenses', 'profit', 'periodLabel'));
    }

    /**
     * Получить диапазон дат для отчётов.
     */
    private function getDateRange($period, $customDate = null)
    {
        if ($customDate) {
            return [$customDate, $customDate, date('d.m.Y', strtotime($customDate))];
        }

        $today = now()->format('Y-m-d');

        return match($period) {
            'today' => [$today, $today, 'Сегодня'],
            'week' => [now()->subDays(7)->format('Y-m-d'), $today, 'Последние 7 дней'],
            'month' => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
            'year' => [now()->subDays(365)->format('Y-m-d'), $today, 'Год'],
            default => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
        };
    }
}
