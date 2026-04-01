<?php

namespace Modules\Owner\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Seller\Models\Expense;
use Modules\Warehouse\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('owner.module.reports.expenses');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $shops = Shop::where('is_active', 1)->get();
        $categories = [
            'rent' => 'Аренда',
            'utilities' => 'Коммуналка',
            'salary' => 'Зарплата',
            'taxes' => 'Налоги',
            'marketing' => 'Маркетинг',
            'office' => 'Офис',
            'other' => 'Прочее'
        ];

        return view('owner::expenses.create', compact('shops', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'shop_id' => 'nullable|exists:shops,id',
            'description' => 'nullable|string|max:500',
        ]);

        $expense = new Expense();
        $expense->expense_number = 'EXP-' . strtoupper(uniqid());
        $expense->type = $validated['category']; // We use category as type too for consistency with the model's getTypeIconAttribute
        $expense->category = $validated['category'];
        $expense->amount = $validated['amount'];
        $expense->expense_date = $validated['expense_date'];
        $expense->shop_id = $validated['shop_id'];
        $expense->description = $validated['description'];
        $expense->user_id = Auth::id();
        $expense->save();

        return redirect()->route('owner.module.reports.expenses')
            ->with('success', 'Расход успешно добавлен');
    }
}
