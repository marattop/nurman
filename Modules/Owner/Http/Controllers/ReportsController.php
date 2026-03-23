<?php

namespace Modules\Owner\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Transaction;
use Modules\Seller\Models\Expense;
use Modules\Warehouse\Models\Purchase;
use Modules\Warehouse\Models\Product;
use Modules\Warehouse\Models\Shop;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $revenue = Transaction::sales()->whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount');
        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('supplier_price * quantity'));
        $grossProfit = $revenue - $costOfGoods;
        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount');
        $purchases = Purchase::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount');
        $netProfit = $grossProfit - $expenses - $purchases;

        return view('owner::reports.index', compact(
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'revenue', 'costOfGoods', 'grossProfit', 'expenses', 'purchases', 'netProfit'
        ));
    }

    public function sales(Request $request)
    {
        $period = $request->get('period', 'month');
        $shopId = $request->get('shop_id');
        $sellerId = $request->get('seller_id');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $query = Transaction::sales()
            ->with(['user', 'shop', 'items.variant.product'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($shopId) $query->where('shop_id', $shopId);
        if ($sellerId) $query->where('user_id', $sellerId);

        $sales = $query->orderBy('created_at', 'desc')->paginate(50);
        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $totalItems = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum('quantity');

        $shops = Shop::where('is_active', 1)->get();
        $sellers = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'seller')
            ->where('users.is_active', 1)
            ->select('users.id', 'users.username', 'users.full_name')
            ->get();

        return view('owner::reports.sales', compact(
            'sales', 'totalSales', 'totalTransactions', 'totalItems',
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'shops', 'sellers', 'shopId', 'sellerId'
        ));
    }

    public function purchases(Request $request)
    {
        $period = $request->get('period', 'month');
        $shopId = $request->get('shop_id');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $query = Purchase::with(['variant.product', 'shop', 'warehouse', 'receivedBy'])
            ->whereBetween('purchase_date', [$dateFrom, $dateTo]);

        if ($shopId) $query->where('shop_id', $shopId);

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(50);
        $totalAmount = $purchases->sum('total_amount');
        $totalQuantity = $purchases->sum('quantity');
        $shops = Shop::where('is_active', 1)->get();

        return view('owner::reports.purchases', compact(
            'purchases', 'totalAmount', 'totalQuantity',
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'shops', 'shopId'
        ));
    }

    public function expenses(Request $request)
    {
        $period = $request->get('period', 'month');
        $shopId = $request->get('shop_id');
        $category = $request->get('category');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $query = Expense::with(['shop', 'user'])
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);

        if ($shopId) $query->where('shop_id', $shopId);
        if ($category) $query->where('category', $category);

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(50);
        $totalAmount = $expenses->sum('amount');
        $shops = Shop::where('is_active', 1)->get();
        $categories = Expense::select('category')->distinct()->pluck('category');

        return view('owner::reports.expenses', compact(
            'expenses', 'totalAmount',
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'shops', 'categories', 'shopId', 'category'
        ));
    }

    public function profit(Request $request)
    {
        $period = $request->get('period', 'month');
        [$dateFrom, $dateTo, $periodLabel] = $this->getDateRange($period);

        $revenue = Transaction::sales()->whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount');
        $costOfGoods = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->sum(DB::raw('supplier_price * quantity'));
        $grossProfit = $revenue - $costOfGoods;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        $expenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('category, SUM(amount) as amount')
            ->groupBy('category')
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = $expenses;
        $purchases = Purchase::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total_amount');
        $netProfit = $grossProfit - $totalExpenses - $purchases;
        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        $dailyProfit = DB::table('transactions')
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(transactions.created_at) as date,
                SUM(transaction_items.price * transaction_items.quantity) as revenue,
                SUM(transaction_items.supplier_price * transaction_items.quantity) as cost,
                SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity) as profit')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $productsProfit = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('transaction_items', 'product_variants.id', '=', 'transaction_items.variant_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sale')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->selectRaw('products.id, products.name, products.article,
                SUM(transaction_items.quantity) as sold_qty,
                SUM(transaction_items.price * transaction_items.quantity) as revenue,
                SUM(transaction_items.supplier_price * transaction_items.quantity) as cost,
                SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity) as profit,
                AVG((transaction_items.price - transaction_items.supplier_price) / NULLIF(transaction_items.price, 0) * 100) as margin')
            ->groupBy('products.id')
            ->orderBy('profit', 'desc')
            ->limit(20)
            ->get();

        $shopsProfit = DB::table('shops')
            ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                $join->on('shops.id', '=', 'transactions.shop_id')
                     ->where('transactions.type', 'sale')
                     ->whereBetween('transactions.created_at', [$dateFrom, $dateTo]);
            })
            ->leftJoin('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->selectRaw('shops.id, shops.name,
                COUNT(DISTINCT transactions.id) as transactions_count,
                COALESCE(SUM(transaction_items.price * transaction_items.quantity), 0) as revenue,
                COALESCE(SUM(transaction_items.supplier_price * transaction_items.quantity), 0) as cost,
                COALESCE(SUM((transaction_items.price - transaction_items.supplier_price) * transaction_items.quantity), 0) as profit')
            ->where('shops.is_active', 1)
            ->groupBy('shops.id')
            ->orderBy('profit', 'desc')
            ->get();

        return view('owner::reports.profit', compact(
            'period', 'periodLabel', 'dateFrom', 'dateTo',
            'revenue', 'costOfGoods', 'grossProfit', 'grossMargin',
            'expenses', 'expensesByCategory', 'totalExpenses', 'purchases', 'netProfit', 'netMargin',
            'dailyProfit', 'productsProfit', 'shopsProfit'
        ));
    }

    public function export(Request $request)
    {
        return response()->json(['success' => true]);
    }

    private function getDateRange(string $period): array
    {
        $today = now()->format('Y-m-d');
        return match($period) {
            'today' => [$today, $today, 'Сегодня'],
            'week' => [now()->subDays(7)->format('Y-m-d'), $today, 'Последние 7 дней'],
            'month' => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
            'year' => [now()->subDays(365)->format('Y-m-d'), $today, 'Последние 365 дней'],
            default => [now()->subDays(30)->format('Y-m-d'), $today, 'Последние 30 дней'],
        };
    }
}
