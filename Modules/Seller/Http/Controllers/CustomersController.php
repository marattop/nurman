<?php

namespace Modules\Seller\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('client') // Предполагаем использование Spatie Roles или наличие метода role
            ->with('customerProfile');

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('seller::customers.index', compact('customers'));
    }

    public function show($id)
    {
        $currentUser = auth()->user();
        
        // Загружаем клиента с транзакциями, фильтруя по магазину текущего продавца
        $customer = User::with(['customerProfile', 'transactions' => function($q) use ($currentUser) {
            // Показываем только транзакции из магазина текущего продавца
            $q->where('shop_id', $currentUser->shop_id)
              ->latest()
              ->limit(10);
        }])->findOrFail($id);

        // Проверяем, что это действительно клиент (роль client)
        if (!$customer->hasRole('client')) {
            abort(403, 'Доступ запрещён');
        }

        return view('seller::customers.show', compact('customer'));
    }
}
