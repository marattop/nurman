<?php

namespace Modules\Seller\Http\Controllers;

use Modules\Core\Models\User;

class CustomersController extends Controller
{
    public function index()
    {
        $customers = User::where('role_id', 5) // ROLE_CLIENT
            ->with('customerProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('seller::customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['customerProfile', 'coupons', 'transactions']);
        return view('seller::customers.show', compact('customer'));
    }
}
