<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Редирект по роли пользователя
     */
    public function redirectByRole()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->isOwner() || $user->isAdmin()) {
            return redirect()->route('owner.dashboard');
        }

        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        }

        if ($user->isWarehouse()) {
            return redirect()->route('warehouse.dashboard');
        }

        if ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }

        return redirect()->route('login');
    }
}
