<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Purchase;

/**
 * Сервис отчётов и аналитики.
 * Оптимизирован для высокой нагрузки с кэшированием.
 */
class ReportService
{
    /**
     * Время кэширования (5 минут).
     */
    const CACHE_TTL = 300;

    /**
     * Получить финансовую сводку.
     */
    public function getFinancialSummary($dateFrom, $dateTo)
    {
        $cacheKey = 'financial_summary_' . md5($dateFrom . '_' . $dateTo);

        return Cache::remember($cacheKey, static::CACHE_TTL, function() use ($dateFrom, $dateTo) {
            // Выручка
            $revenue = Transaction::sales()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total, COALESCE(AVG(total_amount), 0) as average')
                ->first();

            // Себестоимость
            $costOfGoods = DB::table('transaction_items')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
                ->sum(DB::raw('supplier_price * quantity'));

            // Закупки
            $purchases = Purchase::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
                ->first();

            // Расходы
            $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
                ->first();

            // Расчёт показателей
            $grossProfit = $revenue->total - $costOfGoods;
            $grossMargin = $revenue->total > 0 ? ($grossProfit / $revenue->total) * 100 : 0;
            $netProfit = $grossProfit - $purchases->total - $expenses->total;
            $netMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

            return [
                'revenue' => $revenue,
                'cost_of_goods' => $costOfGoods,
                'gross_profit' => $grossProfit,
                'gross_margin' => $grossMargin,
                'purchases' => $purchases,
                'expenses' => $expenses,
                'net_profit' => $netProfit,
                'net_margin' => $netMargin,
            ];
        });
    }

    /**
     * Выручка по источникам оплаты.
     */
    public function getRevenueBySource($dateFrom, $dateTo)
    {
        return Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('payment_method, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Выручка по магазинам.
     */
    public function getRevenueByShop($dateFrom, $dateTo)
    {
        return DB::table('shops')
            ->leftJoin('transactions', fn($j) => $j->on('shops.id', '=', 'transactions.shop_id')
                ->where('transactions.type', 'sale')
                ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]))
            ->selectRaw('shops.name, COUNT(transactions.id) as count, COALESCE(SUM(transactions.total_amount), 0) as total')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Топ товаров по прибыли.
     */
    public function getTopProducts($dateFrom, $dateTo, $limit = 10)
    {
        return DB::table('products')
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
            ->limit($limit)
            ->get();
    }

    /**
     * Динамика выручки по дням.
     */
    public function getRevenueDynamics($dateFrom, $dateTo)
    {
        return Transaction::sales()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Расходы по категориям.
     */
    public function getExpensesByCategory($dateFrom, $dateTo)
    {
        return Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('type as category, SUM(amount) as total')
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * Очистка кэша отчётов.
     */
    public function clearCache()
    {
        Cache::tags(['reports', 'financial'])->flush();
    }
}
