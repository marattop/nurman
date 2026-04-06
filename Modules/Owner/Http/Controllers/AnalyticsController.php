<?php

namespace Modules\Owner\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Transaction;
use Modules\Seller\Models\Expense;
use Modules\Warehouse\Models\Purchase;
use Modules\Warehouse\Models\Shop;

class AnalyticsController extends Controller
{
    public function index(Request $request)
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

        $salesCount = Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $customersCount = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'client')
            ->where('users.is_active', 1)
            ->count();

        $topProducts = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('products.id, products.name, products.article,
                SUM(transaction_items.quantity) as sold_qty,
                SUM(transaction_items.price * transaction_items.quantity) as revenue')
            ->groupBy('products.id')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

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

        return view('owner::analytics.index', compact(
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'revenue', 'grossProfit', 'netProfit',
            'salesCount', 'customersCount',
            'topProducts', 'revenueByShop'
        ));
    }

    public function api(Request $request)
    {
        $period = $request->get('period', 'today');
        
        [$dateFrom, $dateTo] = $this->getDateRange($period);
        
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
        
        $salesCount = Transaction::sales()->whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $avgCheck = $salesCount > 0 ? $revenue / $salesCount : 0;
        
        $revenueByShop = DB::table('shops')
            ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                $join->on('shops.id', '=', 'transactions.shop_id')
                     ->where('transactions.type', 'sale')
                     ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
            })
            ->selectRaw('shops.name, COALESCE(SUM(transactions.total_amount), 0) as total')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('total', 'desc')
            ->get();
        
        $recentSales = Transaction::sales()
            ->with(['user', 'shop'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $hourlyRevenue = $period === 'today'
            ? Transaction::sales()->whereDate('created_at', $dateFrom)
                ->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as revenue')
                ->groupBy('hour')->orderBy('hour')->get()
            : Transaction::sales()->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                ->groupBy('date')->orderBy('date')->get();
        
        return response()->json([
            'success' => true,
            'period' => $period,
            'metrics' => [
                'revenue' => round($revenue, 2),
                'costOfGoods' => round($costOfGoods, 2),
                'grossProfit' => round($grossProfit, 2),
                'grossMargin' => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0,
                'expenses' => round($expenses, 2),
                'purchases' => round($purchases, 2),
                'netProfit' => round($netProfit, 2),
                'netMargin' => $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0,
                'salesCount' => $salesCount,
                'avgCheck' => round($avgCheck, 2),
            ],
            'revenueByShop' => $revenueByShop,
            'recentSales' => $recentSales->map(fn($s) => [
                'id' => $s->id,
                'amount' => $s->total_amount,
                'shop' => $s->shop?->name,
                'seller' => $s->user?->full_name ?? $s->user?->username,
                'time' => $s->created_at->format('H:i')
            ]),
            'dynamics' => $hourlyRevenue
        ]);
    }
    
    private function getDateRange(string $period): array
    {
        $now = now();

        return match ($period) {
            'today' => [
                $now->format('Y-m-d'), 
                $now->format('Y-m-d'), 
                'Сегодня'
            ],
            'yesterday' => [
                $now->copy()->subDay()->format('Y-m-d'), 
                $now->copy()->subDay()->format('Y-m-d'), 
                'Вчера'
            ],
            'week' => [
                $now->copy()->startOfWeek()->format('Y-m-d'), 
                $now->copy()->endOfWeek()->format('Y-m-d'), 
                'Эта неделя'
            ],
            'month' => [
                $now->copy()->startOfMonth()->format('Y-m-d'), 
                $now->copy()->endOfMonth()->format('Y-m-d'), 
                'Этот месяц'
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth()->format('Y-m-d'), 
                $now->copy()->subMonth()->endOfMonth()->format('Y-m-d'), 
                'Прошлый месяц'
            ],
            'year' => [
                $now->copy()->startOfYear()->format('Y-m-d'), 
                $now->copy()->endOfYear()->format('Y-m-d'), 
                'Этот год'
            ],
            default => [
                $now->copy()->startOfMonth()->format('Y-m-d'), 
                $now->copy()->endOfMonth()->format('Y-m-d'), 
                'Этот месяц'
            ],
        };
    }
}
