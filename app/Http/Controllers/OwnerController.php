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
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\QrCode;
use App\Models\TransactionItem;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    const CACHE_TTL = 300;

    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Если есть date_from и date_to - используем их
        if ($dateFrom && $dateTo) {
            $periodLabel = 'Произвольный период';
        } else {
            [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);
        }

        // ВРЕМЕННО ОТКЛЮЧАЕМ КЕШ - всегда пересчитываем
        $data = $this->getDashboardData($dateFrom, $dateTo, $periodLabel, $period, $request);

        return view('owner::dashboard', $data);
    }

    private function getDashboardData($dateFrom, $dateTo, $periodLabel, $period, $request = null)
    {
        // Добавляем время чтобы захватить весь день
        $dateFromFull = $dateFrom . ' 00:00:00';
        $dateToFull = $dateTo . ' 23:59:59';

        $revenue = Transaction::sales()
            ->whereBetween('created_at', [$dateFromFull, $dateToFull])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
            ->first();

        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFromFull, $dateToFull])
            ->sum(DB::raw('COALESCE(supplier_price, 0) * quantity'));

        $grossProfit = $revenue->total - $costOfGoods;
        $grossMargin = $revenue->total > 0 ? ($grossProfit / $revenue->total) * 100 : 0;

        // Закупки не вычитаем из прибыли - это пополнение запасов (актив), а не расход
        // Расходы считаются отдельно (аренда, зарплаты, коммунальные и т.д.)
        $purchases = Purchase::whereBetween('purchase_date', [$dateFromFull, $dateToFull])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->first();

        $expenses = Expense::whereBetween('expense_date', [$dateFromFull, $dateToFull])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
            ->first();

        // Чистая прибыль = Валовая прибыль - Операционные расходы
        // Закупки НЕ вычитаем - они уже учтены в себестоимости проданных товаров
        $netProfit = $grossProfit - $expenses->total;
        $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

        $revenueBySource = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get();

        $revenueByShop = DB::table('shops')
            ->leftJoin('transactions', fn($j) => $j->on('shops.id', '=', 'transactions.shop_id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('shops.name, COUNT(transactions.id) as count, COALESCE(SUM(transactions.total_amount), 0) as total')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('total', 'desc')
            ->get();

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

        $recentSales = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'shop'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $staffStats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'sellers' => User::where('role_id', 3)->count(),
            'warehouse' => User::where('role_id', 4)->count(),
        ];

        $totalShops = DB::table('shops')->where('is_active', 1)->count();

        $totalEmployees = User::where('is_active', true)->count();

        $expensesByCategory = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('type as category, SUM(amount) as total')
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();

        $revenueDynamics = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $profitDynamics = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(transactions.created_at) as date,
                SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity) as profit')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // СТАТИСТИКА ПО ДНЯМ для календаря
        $dailyStats = DB::table('transactions')
            ->where('type', 'sale')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, 
                COUNT(*) as sales_count,
                COALESCE(SUM(total_amount), 0) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Закупки по дням
        $dailyPurchases = Purchase::whereBetween('purchase_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->selectRaw('DATE(purchase_date) as date, 
                COUNT(*) as purchases_count,
                COALESCE(SUM(total_amount), 0) as cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Расходы по дням
        $dailyExpenses = Expense::whereBetween('expense_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->selectRaw('DATE(expense_date) as date, 
                COUNT(*) as expenses_count,
                COALESCE(SUM(amount), 0) as expenses')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $prevDateFrom = date('Y-m-d', strtotime($dateFrom . ' -1 month'));
        $prevDateTo = date('Y-m-d', strtotime($dateTo . ' -1 month'));
        $prevRevenue = Transaction::sales()
            ->whereBetween('created_at', [$prevDateFrom, $prevDateTo])
            ->sum('total_amount');
        $revenueGrowth = $prevRevenue > 0 ? (($revenue->total - $prevRevenue) / $prevRevenue) * 100 : 0;

        $prevProfit = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$prevDateFrom, $prevDateTo])
            ->sum(DB::raw('(transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity'));
        $profitGrowth = $prevProfit > 0 ? (($netProfit - $prevProfit) / $prevProfit) * 100 : 0;

        $prevExpenses = Expense::whereBetween('created_at', [$prevDateFrom, $prevDateTo])->sum('amount');
        $expensesGrowth = $prevExpenses > 0 ? (($expenses->total - $prevExpenses) / $prevExpenses) * 100 : 0;

        try {
            $purchases->quantity = DB::table('purchases')
                ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->whereBetween('purchases.created_at', [$dateFrom, $dateTo])
                ->sum('purchase_items.quantity');
        } catch (\Exception $e) {
            $purchases->quantity = 0;
        }

        // Средний чек
        $avgCheck = $revenue->count > 0 ? $revenue->total / $revenue->count : 0;

        // Общая стоимость запасов
        $inventoryValue = DB::table('stock')
            ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
            ->sum(DB::raw('COALESCE(product_variants.supplier_price, 0) * stock.quantity'));

        return compact(
            'period', 'periodLabel', 'revenue', 'costOfGoods', 'grossProfit', 'grossMargin',
            'purchases', 'expenses', 'netProfit', 'netMargin',
            'revenueBySource', 'revenueByShop', 'topProducts', 'recentSales', 'staffStats',
            'totalShops', 'totalEmployees', 'expensesByCategory',
            'revenueDynamics', 'profitDynamics',
            'revenueGrowth', 'profitGrowth', 'expensesGrowth',
            'dateFrom', 'dateTo',
            'dailyStats', 'dailyPurchases', 'dailyExpenses',
            'avgCheck', 'inventoryValue'
        );
    }

    public function finance(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $income = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('expense_date as date, type, SUM(amount) as amount')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        return view('owner.finance.index', compact('income', 'expenses', 'period', 'periodLabel'));
    }

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
            ->selectRaw('products.id, products.name, products.article, products.photo,
                AVG(product_variants.supplier_price) as avg_supplier_price,
                AVG(transaction_items.price) as avg_retail_price,
                SUM(transaction_items.quantity) as sold_qty')
            ->groupBy('products.id', 'products.name', 'products.article', 'products.photo')
            ->havingRaw('sold_qty > 0')
            ->orderBy('avg_retail_price', 'desc')
            ->paginate(50);

        return view('owner.finance.margin', compact('products', 'periodLabel', 'period'));
    }

    public function balance()
    {
        $cash = Transaction::sales()->sum('total_amount');
        $inventoryValue = DB::table('stock')
            ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
            ->sum(DB::raw('product_variants.supplier_price * stock.quantity'));

        $accountsPayable = 0;

        $totalAssets = $cash + $inventoryValue;
        $totalLiabilities = $accountsPayable;
        $equity = $totalAssets - $totalLiabilities;

        return view('owner.finance.balance', compact('cash', 'inventoryValue', 'accountsPayable', 'totalAssets', 'totalLiabilities', 'equity'));
    }

    public function users(Request $request)
    {
        // Показываем ТОЛЬКО сотрудников (без клиентов role_id=5)
        $query = User::with('role')
            ->whereIn('role_id', [2, 3, 4]); // admin, seller, warehouse

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
        $roles = Role::whereIn('id', [2, 3, 4])->get();

        return view('owner.users.index', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::getStaffRoles();
        return view('owner.users.create', compact('roles'));
    }

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

    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('owner.users.edit', compact('user', 'roles'));
    }

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

    public function exportLogs(Request $request)
    {
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

    public function reports()
    {
        return view('owner.reports.index');
    }

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

        return view('owner.reports.sales', compact('sales', 'totalSales', 'totalTransactions', 'periodLabel', 'period'));
    }

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
            ->selectRaw('products.id, products.name, products.article, products.photo, products.category_id,
                COUNT(DISTINCT product_variants.id) as variants_count,
                COALESCE(SUM(transaction_items.quantity), 0) as sold_qty,
                COALESCE(SUM(transaction_items.price * transaction_items.quantity), 0) as revenue')
            ->groupBy('products.id', 'products.name', 'products.article', 'products.photo', 'products.category_id')
            ->orderBy('sold_qty', 'desc')
            ->paginate(50);

        return view('owner.reports.products', compact('products', 'periodLabel', 'period'));
    }

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

        return view('owner.reports.finance', compact('revenue', 'costOfGoods', 'expenses', 'profit', 'periodLabel', 'period'));
    }

    public function purchasesReport(Request $request)
    {
        $period = $request->get('period', 'month');
        $shopId = $request->get('shop_id');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $shops = Shop::all();

        // Закупки из таблицы purchases (админские закупки)
        $query = Purchase::with(['variant.product', 'shop', 'warehouse', 'receivedBy'])
            ->whereBetween('purchase_date', [$dateFrom, $dateTo]);

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $purchases = $query->orderByDesc('created_at')->paginate(50);

        $totalAmount = $purchases->sum('total_amount');
        $totalQuantity = $purchases->sum('quantity');

        return view('owner.reports.purchases', compact('purchases', 'totalAmount', 'totalQuantity', 'periodLabel', 'period', 'shops', 'shopId'));
    }

    public function profitReport(Request $request)
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
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount');
        $expensesByCategory = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        $netProfit = $grossProfit - $expenses;
        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        $productsProfit = DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->leftJoin('transactions', fn($j) => $j->on('transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('products.id, products.name, products.article,
                COALESCE(SUM(transaction_items.quantity), 0) as sold_qty,
                COALESCE(SUM((transaction_items.price - product_variants.supplier_price) * transaction_items.quantity), 0) as profit')
            ->groupBy('products.id', 'products.name', 'products.article')
            ->orderBy('profit', 'desc')
            ->limit(10)
            ->get();

        return view('owner.reports.profit', compact('revenue', 'costOfGoods', 'grossProfit', 'grossMargin', 'expenses', 'expensesByCategory', 'netProfit', 'netMargin', 'productsProfit', 'periodLabel', 'period'));
    }

    public function expensesReport(Request $request)
    {
        $period = $request->get('period', 'month');
        $category = $request->get('category');
        $shopId = $request->get('shop_id');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $categories = ['rent', 'utilities', 'salary', 'taxes', 'marketing', 'other'];
        $shops = Shop::all();

        $query = Expense::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($category) {
            $query->where('category', $category);
        }

        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $expenses = $query->orderBy('created_at', 'desc')->paginate(50);

        $totalAmount = $expenses->sum('amount');

        return view('owner.reports.expenses', compact('expenses', 'totalAmount', 'periodLabel', 'period', 'categories', 'category', 'shops', 'shopId'));
    }

    /**
     * Создание расхода
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'shop_id' => 'nullable|exists:shops,id',
            'description' => 'nullable|string',
        ]);

        Expense::create([
            'expense_date' => $validated['expense_date'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'shop_id' => $validated['shop_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('owner.reports.expenses')
            ->with('success', 'Расход успешно добавлен');
    }

    private function getDateRange($period, $customDate = null)
    {
        if ($customDate) {
            return [$customDate, $customDate, date('d.m.Y', strtotime($customDate))];
        }

        $now = now();
        $todayStart = $now->copy()->startOfDay()->format('Y-m-d H:i:s');
        $todayEnd = $now->copy()->endOfDay()->format('Y-m-d H:i:s');

        return match($period) {
            'today' => [$todayStart, $todayEnd, 'Сегодня'],
            'yesterday' => [
                $now->copy()->subDay()->startOfDay()->format('Y-m-d H:i:s'),
                $now->copy()->subDay()->endOfDay()->format('Y-m-d H:i:s'),
                'Вчера',
            ],
            'week' => [
                $now->copy()->startOfWeek()->format('Y-m-d H:i:s'),
                $now->copy()->endOfWeek()->format('Y-m-d H:i:s'),
                'Эта неделя',
            ],
            'month' => [
                $now->copy()->startOfMonth()->format('Y-m-d H:i:s'),
                $now->copy()->endOfMonth()->format('Y-m-d H:i:s'),
                'Этот месяц',
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'),
                $now->copy()->subMonth()->endOfMonth()->format('Y-m-d H:i:s'),
                'Прошлый месяц',
            ],
            'year' => [
                $now->copy()->startOfYear()->format('Y-m-d H:i:s'),
                $now->copy()->endOfYear()->format('Y-m-d H:i:s'),
                'Этот год',
            ],
            default => [
                $now->copy()->startOfMonth()->format('Y-m-d H:i:s'),
                $now->copy()->endOfMonth()->format('Y-m-d H:i:s'),
                'Этот месяц',
            ],
        };
    }

    /**
     * Страница сканера QR-кодов для владельца
     */
    public function scanQr()
    {
        return view('owner.scan-product');
    }

    /**
     * Обработка сканирования QR-кода или штрих-кода товара
     */
    public function processProductScan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string', 'max:255'],
        ]);

        $qrData = trim($request->qr_data);

        \Log::debug('Owner QR scan: ' . mb_substr($qrData, 0, 100));

        $product = null;
        $foundBy = null;

        try {
            $parsed = json_decode($qrData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                if (isset($parsed['article'])) {
                    $article = $parsed['article'];

                    $product = Product::where('article', $article)->first();
                    $foundBy = 'json_article';
                }

                if (!$product && isset($parsed['barcode'])) {
                    $barcode = $parsed['barcode'];

                    $variant = ProductVariant::where('barcode', $barcode)->first();
                    if ($variant) {
                        $product = $variant->product;
                        $foundBy = 'json_barcode';
                    }
                }
            } else {
                $product = Product::where('article', $qrData)->first();
                if ($product) {
                    $foundBy = 'article';

                } else {
                    $variant = ProductVariant::where('barcode', $qrData)->first();
                    if ($variant) {
                        $product = $variant->product;
                        $foundBy = 'barcode';

                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Owner QR parse error: ' . $e->getMessage());

            $product = Product::where('article', $qrData)->first();
            if ($product) {
                $foundBy = 'article';
            } else {
                $variant = ProductVariant::where('barcode', $qrData)->first();
                if ($variant) {
                    $product = $variant->product;
                    $foundBy = 'barcode';
                }
            }
        }

        if (!$product) {


            return response()->json([
                'success' => false,
                'message' => 'Товар не найден',
                'scanned_data' => $qrData,
                'searched_by' => $foundBy,
            ]);
        }

        \Log::debug('Owner QR found: ' . $product->article . ' by ' . $foundBy);

        $variants = $this->getVariantsWithStock($product);

        $isKnownQr = QrCode::whereIn('variant_id', function ($q) use ($product) {
            $q->select('id')->from('product_variants')->where('product_id', $product->id);
        })->exists();

        $totalStock = $variants->sum('total_quantity');
        $totalShowcase = $variants->sum('showcase_quantity');
        $totalWarehouse = $variants->sum('warehouse_quantity');
        $totalSold = $this->getTotalSold($product);

        return response()->json([
            'success' => true,
            'is_known_qr' => $isKnownQr,
            'found_by' => $foundBy,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'brand' => $product->brand,
                'category' => $product->category?->name ?? '—',
                'photo' => $product->photo ? '/storage/' . $product->photo : null,
                'retail_price' => $product->retail_price ?? 0,
                'supplier_price' => $product->supplier_price ?? 0,
                'wholesale_price' => $product->wholesale_price ?? 0,
                'variants' => $variants,
            ],
            'summary' => [
                'total_stock' => $totalStock,
                'shop_stock' => $totalShowcase,
                'warehouse_stock' => $totalWarehouse,
                'sold' => $totalSold,
                'variants_count' => $variants->count(),
            ],
            'variants' => $variants,
        ]);
    }

    /**
     * Получение всех вариантов товара с остатками по магазинам и складам
     */
    private function getVariantsWithStock($product)
    {
        $variants = ProductVariant::where('product_id', $product->id)
            ->with(['stock.shop'])
            ->get()
            ->map(function ($variant) {
                $stockData = $variant->stock->groupBy(function ($item) {
                    return $item->shop_id ? 'shop' : 'warehouse';
                });

                $shops = [];
                $showcaseQty = 0;
                $warehouseQty = 0;

                if ($stockData->has('shop')) {
                    foreach ($stockData->get('shop') as $stock) {
                        $shops[] = [
                            'id' => $stock->shop->id ?? null,
                            'name' => $stock->shop->name ?? '—',
                            'quantity' => $stock->quantity,
                            'reserved' => $stock->reserved,
                            'available' => $stock->quantity - $stock->reserved,
                        ];
                        $showcaseQty += $stock->quantity;
                    }
                }

                if ($stockData->has('warehouse')) {
                    foreach ($stockData->get('warehouse') as $stock) {
                        $warehouseQty += $stock->quantity;
                    }
                }

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'barcode' => $variant->barcode,
                    'wholesale_price' => number_format($variant->wholesale_price ?? 0, 2, ',', ' '),
                    'supplier_price' => number_format($variant->supplier_price ?? 0, 2, ',', ' '),
                    'total_quantity' => $showcaseQty + $warehouseQty,
                    'showcase_quantity' => $showcaseQty,
                    'warehouse_quantity' => $warehouseQty,
                    'shops' => $shops,
                ];
            });

        return $variants;
    }

    /**
     * Получение общего количества проданных товаров
     */
    private function getTotalSold($product)
    {
        return TransactionItem::whereHas('variant', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->sum('quantity');
    }

    /**
     * Страница редактирования товара (для владельца)
     */
    public function editProduct(Product $product)
    {
        $categories = Category::all();
        $product->load(['variants.qrCodes', 'category']);
        
        return view('owner.products.edit', compact('product', 'categories'));
    }

    /**
     * Обновление товара (для владельца)
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'retail_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'article' => $validated['article'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'brand' => $validated['brand'],
            'material' => $validated['material'],
            'country' => $validated['country'],
            'retail_price' => $validated['retail_price'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('photo')) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        if ($request->hasFile('photos')) {
            if ($product->photos) {
                foreach ($product->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('products', 'public');
            }
            $data['photos'] = $photoPaths;
        }

        $product->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_update',
            'description' => "Владелец обновил товар: {$product->name} ({$product->article})",
            'model_type' => Product::class,
            'model_id' => $product->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('owner.scan-product')->with('success', 'Товар обновлён');
    }

    /**
     * Полная аналитика бизнеса
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($dateFrom && $dateTo) {
            $periodLabel = 'Произвольный период';
        } else {
            [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period, null);
        }

        // Продажи по часам
        $salesByHour = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Продажи по дням недели
        $salesByDay = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DAYNAME(created_at) as day_name, DAYOFWEEK(created_at) as day_num, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('day_name', 'day_num')
            ->orderBy('day_num')
            ->get();

        // Топ продавцов
        $topSellers = DB::table('users')
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('users.full_name, users.username, COUNT(*) as sales_count, SUM(transactions.total_amount) as total_revenue')
            ->groupBy('users.id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Топ магазинов
        $topShops = DB::table('shops')
            ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                $join->on('shops.id', '=', 'transactions.shop_id')
                     ->where('transactions.type', 'sale')
                     ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
            })
            ->selectRaw('shops.name, COUNT(transactions.id) as sales_count, COALESCE(SUM(transactions.total_amount), 0) as total_revenue')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Средний чек
        $avgCheck = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('AVG(total_amount) as avg, MIN(total_amount) as min, MAX(total_amount) as max')
            ->first();

        // Возвраты
        $returns = Transaction::where('type', 'return')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('COUNT(*) as count, SUM(total_amount) as total')
            ->first();

        // Активность по месяцам
        $monthlyActivity = Transaction::sales()
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('owner.analytics.index', compact(
            'periodLabel', 'dateFrom', 'dateTo',
            'salesByHour', 'salesByDay', 'topSellers', 'topShops',
            'avgCheck', 'returns', 'monthlyActivity'
        ));
    }
}
