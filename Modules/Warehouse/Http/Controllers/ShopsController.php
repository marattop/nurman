<?php

namespace Modules\Warehouse\Http\Controllers;

use Modules\Warehouse\Models\Shop;

class ShopsController extends Controller
{
    public function index()
    {
        $shops = Shop::all();
        return view('warehouse::shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->load(['stock.variant.product']);
        return view('warehouse::shops.show', compact('shop'));
    }
}
