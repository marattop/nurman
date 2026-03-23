<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SellerSettingsController extends Controller
{
    /**
     * Страница настроек продавца
     */
    public function index($sellerId)
    {
        $seller = User::with(['shop', 'role'])->findOrFail($sellerId);
        $shops = Shop::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('admin.sellers.settings', compact('seller', 'shops', 'warehouses'));
    }
    
    /**
     * Обновление настроек продавца
     */
    public function update(Request $request, $sellerId)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'can_sell_from_warehouse' => 'boolean',
        ]);
        
        $seller = User::findOrFail($sellerId);
        
        $seller->update([
            'shop_id' => $request->shop_id,
            'can_sell_from_warehouse' => $request->has('can_sell_from_warehouse'),
        ]);
        
        return back()->with('success', "Настройки продавца '{$seller->full_name}' обновлены");
    }
}
