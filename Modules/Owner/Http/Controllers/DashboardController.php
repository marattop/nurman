<?php

namespace Modules\Owner\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Transaction;
use Modules\Seller\Models\Expense;
use Modules\Warehouse\Models\Purchase;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\Shop;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Главная панель владельца
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $customDate = $request->get('custom_date');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($dateFrom && $dateTo) {
            $periodLabel = date('d.m.Y', strtotime($dateFrom)) . ' — ' . date('d.m.Y', strtotime($dateTo));
        } else {
            [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period, $customDate);
        }

        // === КЭШИРОВАНИЕ ДАННЫХ (5 минут) ===
        $cacheKey = "owner_dashboard_{$period}_{$dateFrom}_{$dateTo}_" . md5(auth()->id());
        
        $data = cache()->remember($cacheKey, now()->addMinutes(5), function() use ($period, $dateFrom, $dateTo) {
            // Выручка
            $revenue = Transaction::sales()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
                ->first();

            // Себестоимость проданных товаров (оптимизировано)
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
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(SUM(quantity), 0) as quantity')
                ->first();

            // Расходы
            $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
                ->first();

            // Чистая прибыль
            $netProfit = $grossProfit - $purchases->total - $expenses->total;
            $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

            // === ДИНАМИКА ПО ДНЯМ (исправлено - нормализованные данные) ===
            $revenueDynamics = Transaction::sales()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            // Прибыль по дням (корректный расчёт через подзапрос)
            $profitDynamics = DB::table(DB::raw('(
                SELECT DATE(t.created_at) as date, 
                       SUM((ti.price - ti.supplier_price) * ti.quantity) as profit
                FROM transactions t
                JOIN transaction_items ti ON t.id = ti.transaction_id
                WHERE t.type = "sale" 
                  AND t.created_at BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'"
                GROUP BY DATE(t.created_at)
            ) as daily_profit'))
                ->select('date', 'profit')
                ->orderBy('date', 'ASC')
                ->get();

            // === ТРАФИК И КОНВЕРСИЯ ===
            $totalItemsSold = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
                ->sum('quantity');

            $avgCheck = $revenue->count > 0 ? $revenue->total / $revenue->count : 0;
            $avgItemsPerCheck = $revenue->count > 0 ? $totalItemsSold / $revenue->count : 0;

            // === СРАВНЕНИЕ С ПРЕДЫДУЩИМ ПЕРИОДОМ ===
            $prevPeriod = $this->getPrevPeriod($period, $dateFrom, $dateTo);
            $prevRevenue = Transaction::sales()
                ->whereBetween('created_at', [$prevPeriod['from'], $prevPeriod['to']])
                ->sum('total_amount');

            $revenueGrowth = $prevRevenue > 0 ? (($revenue->total - $prevRevenue) / $prevRevenue) * 100 : 0;

            $prevExpenses = Expense::whereBetween('created_at', [$prevPeriod['from'], $prevPeriod['to']])->sum('amount');
            $expensesGrowth = $prevExpenses > 0 ? (($expenses->total - $prevExpenses) / $prevExpenses) * 100 : 0;

            $prevNetProfit = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$prevPeriod['from'], $prevPeriod['to']])
                ->sum(DB::raw('(price - supplier_price) * quantity')) - $prevExpenses;

            $profitGrowth = $prevNetProfit != 0 ? (($netProfit - $prevNetProfit) / abs($prevNetProfit)) * 100 : 0;

            // === ВЫРУЧКА ПО ИСТОЧНИКАМ ОПЛАТЫ ===
            $revenueBySource = Transaction::sales()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
                ->groupBy('payment_method')
                ->orderBy('total', 'desc')
                ->get();

            // === ВЫРУЧКА ПО МАГАЗИНАМ ===
            $revenueByShop = DB::table('shops')
                ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                    $join->on('shops.id', '=', 'transactions.shop_id')
                         ->where('transactions.type', 'sale')
                         ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
                })
                ->selectRaw('shops.name, COUNT(transactions.id) as count, COALESCE(SUM(transactions.total_amount), 0) as total')
                ->where('shops.is_active', 1)
                ->groupBy('shops.id')
                ->orderBy('total', 'desc')
                ->get();

            // === ТОП ТОВАРОВ ПО ПРИБЫЛИ (оптимизировано) ===
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

            // === ПОСЛЕДНИЕ ПРОДАЖИ ===
            $recentSales = Transaction::sales()
                ->with(['user', 'shop'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // === СТАТИСТИКА ПО ПРОДАВЦАМ ===
            $sellersStats = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                    $join->on('users.id', '=', 'transactions.user_id')
                         ->where('transactions.type', 'sale')
                         ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
                })
                ->leftJoin('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->where('roles.name', 'seller')
                ->where('users.is_active', 1)
                ->selectRaw('users.id, users.username, users.full_name,
                            COUNT(DISTINCT transactions.id) as sales_count,
                            COALESCE(SUM(transactions.total_amount), 0) as total_revenue,
                            COALESCE(SUM(transaction_items.quantity), 0) as items_sold')
                ->groupBy('users.id')
                ->orderBy('total_revenue', 'desc')
                ->get();

            // === ТОВАРЫ С НИЗКИМ ОСТАТКОМ ===
            $lowStockProducts = DB::table('product_variants')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->join('stock', 'product_variants.id', '=', 'stock.variant_id')
                ->whereColumn('stock.quantity', '<=', 'stock.min_stock')
                ->selectRaw('products.name, products.article, product_variants.size, product_variants.color, stock.quantity, stock.min_stock')
                ->limit(10)
                ->get();

            // === РАСХОДЫ ПО КАТЕГОРИЯМ ===
            $expensesByCategory = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->orderBy('total', 'desc')
                ->get();

            // === ОБЩАЯ СТОИМОСТЬ ЗАПАСОВ ===
            $inventoryValue = DB::table('stock')
                ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
                ->sum(DB::raw('product_variants.supplier_price * stock.quantity'));

            // === ВСЕГО МАГАЗИНОВ И СОТРУДНИКОВ ===
            $totalShops = Shop::where('is_active', 1)->count();
            $totalEmployees = User::where('is_active', 1)->whereHas('role', fn($q) => $q->whereIn('name', ['seller', 'warehouse', 'admin']))->count();

            return [
                'revenue' => $revenue,
                'costOfGoods' => $costOfGoods,
                'grossProfit' => $grossProfit,
                'grossMargin' => $grossMargin,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'netProfit' => $netProfit,
                'netMargin' => $netMargin,
                'revenueDynamics' => $revenueDynamics,
                'profitDynamics' => $profitDynamics,
                'totalItemsSold' => $totalItemsSold,
                'avgCheck' => $avgCheck,
                'avgItemsPerCheck' => $avgItemsPerCheck,
                'revenueGrowth' => $revenueGrowth,
                'expensesGrowth' => $expensesGrowth,
                'profitGrowth' => $profitGrowth,
                'revenueBySource' => $revenueBySource,
                'revenueByShop' => $revenueByShop,
                'topProducts' => $topProducts,
                'recentSales' => $recentSales,
                'sellersStats' => $sellersStats,
                'lowStockProducts' => $lowStockProducts,
                'expensesByCategory' => $expensesByCategory,
                'inventoryValue' => $inventoryValue,
                'totalShops' => $totalShops,
                'totalEmployees' => $totalEmployees,
            ];
        });

        // Извлекаем данные из кэша
        extract($data);

        // Рендерим view с данными из кэша
        return view('owner::dashboard', compact(
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'revenue', 'costOfGoods', 'grossProfit', 'grossMargin',
            'purchases', 'expenses', 'netProfit', 'netMargin',
            'revenueBySource', 'revenueByShop', 'topProducts',
            'recentSales', 'sellersStats', 'lowStockProducts',
            'revenueDynamics', 'profitDynamics',
            'totalItemsSold', 'avgCheck', 'avgItemsPerCheck',
            'revenueGrowth', 'expensesGrowth', 'profitGrowth',
            'expensesByCategory', 'inventoryValue', 'totalShops', 'totalEmployees'
        ));
    }

    /**
     * Получить диапазон дат
     */
    private function getDateRange(string $period, ?string $customDate): array
    {
        if ($customDate) {
            return [$customDate, $customDate, date('d.m.Y', strtotime($customDate))];
        }

        $today = now()->format('Y-m-d');

        return match($period) {
            'today' => [$today, $today, 'Сегодня'],
            'week' => [now()->subDays(7)->format('Y-m-d'), $today, 'Последние 7 дней'],
            'month' => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
            'year' => [now()->subDays(365)->format('Y-m-d'), $today, 'Последние 365 дней'],
            default => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
        };
    }

    /**
     * Получить предыдущий период для сравнения
     */
    private function getPrevPeriod(string $period, string $dateFrom, string $dateTo): array
    {
        $days = max(1, (strtotime($dateTo) - strtotime($dateFrom)) / 86400);

        return match($period) {
            'today' => [
                'from' => date('Y-m-d', strtotime($dateFrom . ' -1 day')),
                'to' => date('Y-m-d', strtotime($dateTo . ' -1 day'))
            ],
            'week' => [
                'from' => date('Y-m-d', strtotime($dateFrom . ' -7 days')),
                'to' => date('Y-m-d', strtotime($dateTo . ' -7 days'))
            ],
            'month' => [
                'from' => date('Y-m-d', strtotime($dateFrom . ' -30 days')),
                'to' => date('Y-m-d', strtotime($dateTo . ' -30 days'))
            ],
            'year' => [
                'from' => date('Y-m-d', strtotime($dateFrom . ' -365 days')),
                'to' => date('Y-m-d', strtotime($dateTo . ' -365 days'))
            ],
            default => [
                'from' => date('Y-m-d', strtotime($dateFrom . ' -30 days')),
                'to' => date('Y-m-d', strtotime($dateTo . ' -30 days'))
            ]
        };
    }
}
