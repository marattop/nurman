<?php

namespace Modules\Owner\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Expense;
use Modules\Seller\Models\Transaction;
use Modules\Warehouse\Models\Purchase;
use Modules\Warehouse\Models\Shop;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $customDate = $request->get('custom_date');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($dateFrom && $dateTo) {
            $periodLabel = date('d.m.Y', strtotime($dateFrom)).' — '.date('d.m.Y', strtotime($dateTo));
        } else {
            [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period, $customDate);
        }

        $cacheKey = "owner_dashboard_{$period}_{$dateFrom}_{$dateTo}_".md5(auth()->id());

        try {
            $data = cache()->remember($cacheKey, now()->addMinute(), function () use ($period, $dateFrom, $dateTo) {
                $rangeStart = Carbon::parse($dateFrom)->startOfDay();
                $rangeEnd = Carbon::parse($dateTo)->endOfDay();

                $revenue = Transaction::sales()
                    ->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo)
                    ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
                    ->first();

                $costOfGoods = DB::table('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.type', 'sale')
                    ->whereDate('transactions.created_at', '>=', $dateFrom)
                    ->whereDate('transactions.created_at', '<=', $dateTo)
                    ->sum(DB::raw('supplier_price * quantity'));

                $grossProfit = $revenue->total - $costOfGoods;
                $grossMargin = $revenue->total > 0 ? ($grossProfit / $revenue->total) * 100 : 0;

                $purchases = Purchase::where(function($q) use ($dateFrom, $dateTo) {
                        $q->whereDate('created_at', '>=', $dateFrom)
                          ->whereDate('created_at', '<=', $dateTo)
                          ->orWhere(function($sq) use ($dateFrom, $dateTo) {
                              $sq->whereDate('purchase_date', '>=', $dateFrom)
                                 ->whereDate('purchase_date', '<=', $dateTo);
                          });
                    })
                    ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(SUM(quantity), 0) as quantity')
                    ->first();

                $expenses = Expense::whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
                    ->first();

                $netProfit = $grossProfit - $expenses->total;
                $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

                $dailySalesSummary = Transaction::sales()
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, COUNT(DISTINCT customer_id) as customers, COALESCE(SUM(total_amount), 0) as revenue, COALESCE(SUM(discount_amount), 0) as discounts')
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();

                $dailyItemsAndProfit = DB::table('transaction_items as ti')
                    ->join('transactions as t', 'ti.transaction_id', '=', 't.id')
                    ->where('t.type', 'sale')
                    ->whereBetween('t.created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(t.created_at) as date, COALESCE(SUM(ti.quantity), 0) as items_sold, COALESCE(SUM(ti.supplier_price * ti.quantity), 0) as cost, COALESCE(SUM((ti.price - ti.supplier_price) * ti.quantity), 0) as gross_profit')
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();

                $dailyExpenses = Expense::whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(created_at) as date, COALESCE(SUM(amount), 0) as total')
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();

                $dailyPurchases = Purchase::whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as total')
                    ->groupBy('date')
                    ->orderBy('date', 'ASC')
                    ->get();

                $dailyPerformance = $this->buildDailyPerformance(
                    $dailySalesSummary,
                    $dailyItemsAndProfit,
                    $dailyExpenses,
                    $dailyPurchases,
                    $dateFrom,
                    $dateTo
                );

                $revenueDynamics = collect($dailyPerformance)
                    ->map(fn (array $day) => [
                        'date' => $day['date'],
                        'revenue' => $day['revenue'],
                        'orders' => $day['orders'],
                    ])
                    ->values();

                $profitDynamics = collect($dailyPerformance)
                    ->map(fn (array $day) => [
                        'date' => $day['date'],
                        'profit' => $day['gross_profit'],
                    ])
                    ->values();

                $totalItemsSold = DB::table('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.type', 'sale')
                    ->whereBetween('transactions.created_at', [$rangeStart, $rangeEnd])
                    ->sum('quantity');

                $avgCheck = $revenue->count > 0 ? $revenue->total / $revenue->count : 0;
                $avgItemsPerCheck = $revenue->count > 0 ? $totalItemsSold / $revenue->count : 0;

                $prevPeriod = $this->getPrevPeriod($period, $dateFrom, $dateTo);
                $prevRangeStart = Carbon::parse($prevPeriod['from'])->startOfDay();
                $prevRangeEnd = Carbon::parse($prevPeriod['to'])->endOfDay();

                $prevRevenue = Transaction::sales()
                    ->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])
                    ->sum('total_amount');

                $revenueGrowth = $prevRevenue > 0 ? (($revenue->total - $prevRevenue) / $prevRevenue) * 100 : 0;

                $prevExpenses = Expense::whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])->sum('amount');
                $expensesGrowth = $prevExpenses > 0 ? (($expenses->total - $prevExpenses) / $prevExpenses) * 100 : 0;

                $prevNetProfit = DB::table('transaction_items')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.type', 'sale')
                    ->whereBetween('transactions.created_at', [$prevRangeStart, $prevRangeEnd])
                    ->sum(DB::raw('(price - supplier_price) * quantity')) - $prevExpenses;

                $profitGrowth = $prevNetProfit != 0 ? (($netProfit - $prevNetProfit) / abs($prevNetProfit)) * 100 : 0;

                $revenueBySource = Transaction::sales()
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
                    ->groupBy('payment_method')
                    ->orderBy('total', 'desc')
                    ->get();

                $revenueByShop = DB::table('shops')
                    ->leftJoin('transactions', function ($join) use ($rangeStart, $rangeEnd) {
                        $join->on('shops.id', '=', 'transactions.shop_id')
                            ->where('transactions.type', 'sale')
                            ->whereBetween('transactions.created_at', [$rangeStart, $rangeEnd]);
                    })
                    ->selectRaw('shops.name, COUNT(transactions.id) as count, COALESCE(SUM(transactions.total_amount), 0) as total')
                    ->where('shops.is_active', 1)
                    ->groupBy('shops.id', 'shops.name')
                    ->orderBy('total', 'desc')
                    ->get();

                $topProducts = DB::table('products')
                    ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->join('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
                    ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                    ->where('transactions.type', 'sale')
                    ->whereBetween('transactions.created_at', [$rangeStart, $rangeEnd])
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
                    ->with(['user', 'shop'])
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();

                $sellersStats = DB::table('users')
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->leftJoin('transactions', function ($join) use ($rangeStart, $rangeEnd) {
                        $join->on('users.id', '=', 'transactions.user_id')
                            ->where('transactions.type', 'sale')
                            ->whereBetween('transactions.created_at', [$rangeStart, $rangeEnd]);
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

                $lowStockProducts = DB::table('product_variants')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->join('stock', 'product_variants.id', '=', 'stock.variant_id')
                    ->whereColumn('stock.quantity', '<=', 'stock.min_stock')
                    ->selectRaw('products.name, products.article, product_variants.size, product_variants.color, stock.quantity, stock.min_stock')
                    ->limit(10)
                    ->get();

                $expensesByCategory = Expense::whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('category, SUM(amount) as total')
                    ->groupBy('category')
                    ->orderBy('total', 'desc')
                    ->get();

                $inventoryValue = DB::table('stock')
                    ->join('product_variants', 'stock.variant_id', '=', 'product_variants.id')
                    ->sum(DB::raw('product_variants.supplier_price * stock.quantity'));

                $totalShops = Shop::where('is_active', 1)->count();
                $totalEmployees = User::where('is_active', 1)->whereHas('role', fn ($q) => $q->whereIn('name', ['seller', 'warehouse', 'admin']))->count();

                $uniqueCustomers = Transaction::sales()
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->whereNotNull('customer_id')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $hourlyPerformance = $this->buildHourlyPerformance($rangeStart, $rangeEnd);

                $weekdayPerformance = collect($dailyPerformance)
                    ->groupBy('weekday_iso')
                    ->map(function ($days, $weekdayIso) {
                        $orders = collect($days)->sum('orders');
                        $revenueValue = collect($days)->sum('revenue');
                        $netProfitValue = collect($days)->sum('net_profit');

                        return [
                            'weekday_iso' => (int) $weekdayIso,
                            'label' => $this->weekdayLabel((int) $weekdayIso),
                            'orders' => $orders,
                            'revenue' => $revenueValue,
                            'net_profit' => $netProfitValue,
                            'avg_check' => $orders > 0 ? $revenueValue / $orders : 0,
                        ];
                    })
                    ->sortBy('weekday_iso')
                    ->values();

                $transactionItemStats = DB::table('transaction_items')
                    ->selectRaw('transaction_id, COALESCE(SUM(quantity), 0) as items_sold, COALESCE(SUM(supplier_price * quantity), 0) as cost, COALESCE(SUM((price - supplier_price) * quantity), 0) as gross_profit')
                    ->groupBy('transaction_id');

                $shopProfitability = DB::table('shops')
                    ->leftJoin('transactions as t', function ($join) use ($rangeStart, $rangeEnd) {
                        $join->on('shops.id', '=', 't.shop_id')
                            ->where('t.type', 'sale')
                            ->whereBetween('t.created_at', [$rangeStart, $rangeEnd]);
                    })
                    ->leftJoinSub($transactionItemStats, 'item_stats', function ($join) {
                        $join->on('t.id', '=', 'item_stats.transaction_id');
                    })
                    ->where('shops.is_active', 1)
                    ->selectRaw('shops.id, shops.name,
                        COUNT(DISTINCT t.id) as orders,
                        COALESCE(SUM(t.total_amount), 0) as revenue,
                        COALESCE(SUM(item_stats.items_sold), 0) as items_sold,
                        COALESCE(SUM(item_stats.cost), 0) as cost,
                        COALESCE(SUM(item_stats.gross_profit), 0) as gross_profit,
                        COALESCE(AVG(t.total_amount), 0) as avg_check')
                    ->groupBy('shops.id', 'shops.name')
                    ->orderBy('gross_profit', 'desc')
                    ->get()
                    ->map(function ($shop) {
                        $shop->margin = $shop->revenue > 0 ? ($shop->gross_profit / $shop->revenue) * 100 : 0;

                        return $shop;
                    });

                $salesAnalytics = [
                    'uniqueCustomers' => $uniqueCustomers,
                    'avgDailyRevenue' => collect($dailyPerformance)->avg('revenue') ?? 0,
                    'avgDailyNetProfit' => collect($dailyPerformance)->avg('net_profit') ?? 0,
                    'avgDailyOrders' => collect($dailyPerformance)->avg('orders') ?? 0,
                    'bestRevenueDay' => collect($dailyPerformance)->sortByDesc('revenue')->first(),
                    'bestProfitDay' => collect($dailyPerformance)->sortByDesc('net_profit')->first(),
                    'bestHour' => collect($hourlyPerformance)->sortByDesc('revenue')->first(),
                    'topShop' => $shopProfitability->sortByDesc('gross_profit')->first(),
                    'topPaymentMethod' => $revenueBySource->sortByDesc('total')->first(),
                ];

                $revenueForecast7 = $this->buildForecast($dailyPerformance, 'revenue', 7);
                $profitForecast7 = $this->buildForecast($dailyPerformance, 'net_profit', 7);
                $revenueForecast30 = $this->buildForecast($dailyPerformance, 'revenue', 30);
                $profitForecast30 = $this->buildForecast($dailyPerformance, 'net_profit', 30);

                $forecastSummary = [
                    'revenueNext7' => $revenueForecast7['total'] ?? 0,
                    'profitNext7' => $profitForecast7['total'] ?? 0,
                    'revenueNext30' => $revenueForecast30['total'] ?? 0,
                    'profitNext30' => $profitForecast30['total'] ?? 0,
                    'confidence' => $revenueForecast7['confidence'] ?? 'Низкая',
                    'revenueTrendPercent' => $revenueForecast7['trendPercent'] ?? 0,
                    'profitTrendPercent' => $profitForecast7['trendPercent'] ?? 0,
                ];

                $forecastSeries = [
                    'actual' => collect($dailyPerformance)->slice(-14)->values(),
                    'revenueForecast' => $revenueForecast7['series'],
                    'profitForecast' => $profitForecast7['series'],
                ];

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
                    'dailyPerformance' => $dailyPerformance,
                    'salesAnalytics' => $salesAnalytics,
                    'weekdayPerformance' => $weekdayPerformance,
                    'hourlyPerformance' => $hourlyPerformance,
                    'shopProfitability' => $shopProfitability,
                    'forecastSummary' => $forecastSummary,
                    'forecastSeries' => $forecastSeries,
                ];
            });
        } catch (\Exception $e) {
            \Log::error('Dashboard cache error: ' . $e->getMessage());
            $data = $this->calculateDashboardData($period, $dateFrom, $dateTo);
        }

        extract($data);

        return view('owner::dashboard', compact(
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'revenue', 'costOfGoods', 'grossProfit', 'grossMargin',
            'purchases', 'expenses', 'netProfit', 'netMargin',
            'revenueBySource', 'revenueByShop', 'topProducts',
            'recentSales', 'sellersStats', 'lowStockProducts',
            'revenueDynamics', 'profitDynamics',
            'totalItemsSold', 'avgCheck', 'avgItemsPerCheck',
            'revenueGrowth', 'expensesGrowth', 'profitGrowth',
            'expensesByCategory', 'inventoryValue', 'totalShops', 'totalEmployees',
            'dailyPerformance', 'salesAnalytics', 'weekdayPerformance', 'hourlyPerformance',
            'shopProfitability', 'forecastSummary', 'forecastSeries'
        ));
    }

    private function getDateRange(string $period, ?string $customDate): array
    {
        if ($customDate) {
            return [$customDate, $customDate, date('d.m.Y', strtotime($customDate))];
        }

        $today = now()->format('Y-m-d');

        return match ($period) {
            'today' => [$today, $today, 'Сегодня'],
            'week' => [now()->subDays(7)->format('Y-m-d'), $today, 'Последние 7 дней'],
            'month' => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
            'year' => [now()->subDays(365)->format('Y-m-d'), $today, 'Последние 365 дней'],
            default => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
        };
    }

    private function getPrevPeriod(string $period, string $dateFrom, string $dateTo): array
    {
        return match ($period) {
            'today' => [
                'from' => date('Y-m-d', strtotime($dateFrom.' -1 day')),
                'to' => date('Y-m-d', strtotime($dateTo.' -1 day')),
            ],
            'week' => [
                'from' => date('Y-m-d', strtotime($dateFrom.' -7 days')),
                'to' => date('Y-m-d', strtotime($dateTo.' -7 days')),
            ],
            'month' => [
                'from' => date('Y-m-d', strtotime($dateFrom.' -30 days')),
                'to' => date('Y-m-d', strtotime($dateTo.' -30 days')),
            ],
            'year' => [
                'from' => date('Y-m-d', strtotime($dateFrom.' -365 days')),
                'to' => date('Y-m-d', strtotime($dateTo.' -365 days')),
            ],
            default => [
                'from' => date('Y-m-d', strtotime($dateFrom.' -30 days')),
                'to' => date('Y-m-d', strtotime($dateTo.' -30 days')),
            ]
        };
    }

    private function buildDailyPerformance($dailySalesSummary, $dailyItemsAndProfit, $dailyExpenses, $dailyPurchases, string $dateFrom, string $dateTo): array
    {
        // Преобразуем коллекции в ассоциативные массивы для более быстрого доступа
        $salesByDate = $dailySalesSummary->pluck('orders', 'date')->toArray();
        $revenueByDate = $dailySalesSummary->pluck('revenue', 'date')->toArray();
        $customersByDate = $dailySalesSummary->pluck('customers', 'date')->toArray();
        $discountsByDate = $dailySalesSummary->pluck('discounts', 'date')->toArray();
        
        $itemsSoldByDate = $dailyItemsAndProfit->pluck('items_sold', 'date')->toArray();
        $costByDate = $dailyItemsAndProfit->pluck('cost', 'date')->toArray();
        $grossProfitByDate = $dailyItemsAndProfit->pluck('gross_profit', 'date')->toArray();
        
        $expensesByDate = $dailyExpenses->pluck('total', 'date')->toArray();
        $purchasesByDate = $dailyPurchases->pluck('total', 'date')->toArray();

        $rows = [];
        $cursor = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->startOfDay();

        while ($cursor->lte($end)) {
            $date = $cursor->format('Y-m-d');
            
            $orders = (int) ($salesByDate[$date] ?? 0);
            $revenueValue = (float) ($revenueByDate[$date] ?? 0);
            $customers = (int) ($customersByDate[$date] ?? 0);
            $discounts = (float) ($discountsByDate[$date] ?? 0);
            $itemsSold = (int) ($itemsSoldByDate[$date] ?? 0);
            $cost = (float) ($costByDate[$date] ?? 0);
            $grossProfitValue = (float) ($grossProfitByDate[$date] ?? 0);
            $expensesValue = (float) ($expensesByDate[$date] ?? 0);
            $purchasesValue = (float) ($purchasesByDate[$date] ?? 0);
            
            $netProfitValue = $grossProfitValue - $expensesValue;
            $avgCheck = $orders > 0 ? $revenueValue / $orders : 0;
            $grossMargin = $revenueValue > 0 ? ($grossProfitValue / $revenueValue) * 100 : 0;
            $netMargin = $revenueValue > 0 ? ($netProfitValue / $revenueValue) * 100 : 0;

            $rows[] = [
                'date' => $date,
                'label' => $cursor->format('d.m'),
                'weekday' => $this->weekdayLabel($cursor->dayOfWeekIso),
                'weekday_iso' => $cursor->dayOfWeekIso,
                'orders' => $orders,
                'customers' => $customers,
                'items_sold' => $itemsSold,
                'revenue' => $revenueValue,
                'discounts' => $discounts,
                'cost' => $cost,
                'gross_profit' => $grossProfitValue,
                'expenses' => $expensesValue,
                'purchases' => $purchasesValue,
                'net_profit' => $netProfitValue,
                'avg_check' => $avgCheck,
                'gross_margin' => $grossMargin,
                'net_margin' => $netMargin,
            ];

            $cursor->addDay();
        }

        return $rows;
    }

    private function buildHourlyPerformance(Carbon $rangeStart, Carbon $rangeEnd)
    {
        $hourlyRows = Transaction::sales()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as revenue, COALESCE(AVG(total_amount), 0) as avg_check')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        return collect(range(0, 23))
            ->map(function ($hour) use ($hourlyRows) {
                $row = $hourlyRows->get($hour);

                return [
                    'hour' => $hour,
                    'label' => sprintf('%02d:00', $hour),
                    'orders' => (int) ($row->orders ?? 0),
                    'revenue' => (float) ($row->revenue ?? 0),
                    'avg_check' => (float) ($row->avg_check ?? 0),
                ];
            })
            ->values();
    }

    private function buildForecast(array $dailyPerformance, string $metric, int $days): array
    {
        $history = collect($dailyPerformance)
            ->pluck($metric)
            ->map(fn ($value) => (float) $value)
            ->values()
            ->all();

        if (empty($history)) {
            return [
                'series' => [],
                'total' => 0,
                'trendPercent' => 0,
                'confidence' => 'Низкая',
            ];
        }

        $window = array_slice($history, -min(count($history), 14));
        $regression = $this->calculateRegression($window);
        $lastDate = Carbon::parse(collect($dailyPerformance)->last()['date']);
        $series = [];

        for ($step = 1; $step <= $days; $step++) {
            $predicted = max(0, $regression['intercept'] + ($regression['slope'] * ($regression['count'] - 1 + $step)));
            $series[] = [
                'date' => $lastDate->copy()->addDays($step)->format('Y-m-d'),
                'label' => $lastDate->copy()->addDays($step)->format('d.m'),
                'value' => round($predicted, 2),
            ];
        }

        $average = $regression['average'];
        $trendPercent = $average > 0 ? ($regression['slope'] / $average) * 100 : 0;

        return [
            'series' => $series,
            'total' => round(collect($series)->sum('value'), 2),
            'trendPercent' => round($trendPercent, 2),
            'confidence' => $this->forecastConfidence($window),
        ];
    }
    
    private function calculateDashboardData($period, $dateFrom, $dateTo)
    {
        $rangeStart = Carbon::parse($dateFrom)->startOfDay();
        $rangeEnd = Carbon::parse($dateTo)->endOfDay();

        $revenue = Transaction::sales()
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
            ->first();

        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$rangeStart, $rangeEnd])
            ->sum(DB::raw('supplier_price * quantity'));

        $grossProfit = $revenue->total - $costOfGoods;
        $grossMargin = $revenue->total > 0 ? ($grossProfit / $revenue->total) * 100 : 0;

        $purchases = Purchase::whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(SUM(quantity), 0) as quantity')
            ->first();

        $expenses = Expense::whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
            ->first();

        $netProfit = $grossProfit - $purchases->total - $expenses->total;
        $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

        return [
            'revenue' => $revenue,
            'costOfGoods' => $costOfGoods,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'purchases' => $purchases,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'netMargin' => $netMargin,
        ];
    }

    private function calculateRegression(array $values): array
    {
        $count = count($values);

        if ($count === 0) {
            return ['slope' => 0, 'intercept' => 0, 'average' => 0, 'count' => 0];
        }

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumXX = 0;

        foreach ($values as $index => $value) {
            $sumX += $index;
            $sumY += $value;
            $sumXY += $index * $value;
            $sumXX += $index * $index;
        }

        $denominator = ($count * $sumXX) - ($sumX * $sumX);
        $slope = $denominator !== 0 ? (($count * $sumXY) - ($sumX * $sumY)) / $denominator : 0;
        $intercept = ($sumY - ($slope * $sumX)) / $count;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'average' => $sumY / $count,
            'count' => $count,
        ];
    }

    private function forecastConfidence(array $values): string
    {
        $count = count($values);
        $average = $count > 0 ? array_sum($values) / $count : 0;

        if ($average <= 0 || $count < 3) {
            return 'Низкая';
        }

        $variance = array_sum(array_map(fn ($value) => ($value - $average) ** 2, $values)) / $count;
        $coefficient = sqrt($variance) / $average;

        return match (true) {
            $coefficient < 0.18 => 'Высокая',
            $coefficient < 0.35 => 'Средняя',
            default => 'Низкая',
        };
    }

    private function weekdayLabel(int $weekdayIso): string
    {
        return match ($weekdayIso) {
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб',
            7 => 'Вс',
            default => '-',
        };
    }
}
