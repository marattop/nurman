<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Корзина хранится в сессии
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('seller::cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $variantId = $validated['variant_id'];
        
        if (isset($cart[$variantId])) {
            $cart[$variantId]['quantity'] += $validated['quantity'];
        } else {
            $variant = \Modules\Warehouse\Models\ProductVariant::with('product')->find($variantId);
            $cart[$variantId] = [
                'variant_id' => $variantId,
                'product_name' => $variant->product->name,
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $variant->retail_price,
                'quantity' => $validated['quantity'],
            ];
        }
        
        session()->put('cart', $cart);
        
        return back()->with('success', 'Товар добавлен в корзину');
    }

    public function remove(Request $request)
    {
        $variantId = $request->get('variant_id');
        $cart = session()->get('cart', []);
        
        unset($cart[$variantId]);
        session()->put('cart', $cart);
        
        return back()->with('success', 'Товар удален из корзины');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Корзина очищена');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        
        if (isset($cart[$validated['variant_id']])) {
            $cart[$validated['variant_id']]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }
        
        return back()->with('success', 'Корзина обновлена');
    }
}
