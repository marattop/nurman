<?php

namespace Modules\Seller\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Seller\Models\Transaction;
use Modules\Seller\Models\TransactionItem;
use Modules\Seller\Models\Coupon;
use Modules\Warehouse\Models\ProductVariant;
use Modules\Warehouse\Models\Stock;
use Modules\Warehouse\Models\Shop;
use Modules\Client\Models\CustomerPurchaseHistory;

class SaleController extends Controller
{
    /**
     * Страница продажи (POS)
     */
    public function index(Request $request)
    {
        $shopId = $request->get('shop_id', auth()->user()->shops->first()?->id ?? 1);
        
        // Товары в наличии
        $products = ProductVariant::with(['product', 'stock'])
            ->whereHas('stock', fn($q) => $q->where('quantity', '>', 0))
            ->get();
        
        return view('seller::sale.index', compact('products', 'shopId'));
    }

    /**
     * Обработка продажи
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,transfer',
            'customer_id' => 'nullable|exists:users,id',
            'coupon_code' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Рассчитать общую сумму
            $totalAmount = 0;
            $items = [];
            
            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);
                $stock = Stock::where('variant_id', $variant->id)
                    ->where('shop_id', $validated['shop_id'])
                    ->first();
                
                if (!$stock || $stock->available < $item['quantity']) {
                    return back()->withErrors(['items' => "Товар {$variant->product->name} недоступен в количестве {$item['quantity']}"]);
                }
                
                $price = $variant->retail_price;
                $subtotal = $price * $item['quantity'];
                
                $totalAmount += $subtotal;
                $items[] = [
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'supplier_price' => $variant->supplier_price,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Применить купон
            $coupon = null;
            $discountAmount = 0;
            
            if (!empty($validated['coupon_code']) && $validated['customer_id']) {
                $coupon = Coupon::where('code', $validated['coupon_code'])
                    ->where('customer_id', $validated['customer_id'])
                    ->first();
                
                if ($coupon && $coupon->isValid()) {
                    $discountAmount = $coupon->calculateDiscount($totalAmount);
                }
            }
            
            $finalAmount = $totalAmount - $discountAmount;
            
            // Создать транзакцию
            $transaction = Transaction::create([
                'transaction_number' => 'TRX-' . date('Ymd') . '-' . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT),
                'type' => 'sale',
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'payment_method' => $validated['payment_method'],
                'shop_id' => $validated['shop_id'],
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'coupon_id' => $coupon?->id,
                'comment' => $validated['comment'] ?? null,
            ]);
            
            // Создать позиции транзакции и обновить остатки
            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'variant_id' => $item['variant']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'supplier_price' => $item['supplier_price'],
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                ]);
                
                // Уменьшить остаток
                $stock = Stock::where('variant_id', $item['variant']->id)
                    ->where('shop_id', $validated['shop_id'])
                    ->first();
                $stock->removeQuantity($item['quantity']);
            }
            
            // Использовать купон
            if ($coupon) {
                $coupon->use();
            }
            
            // Обновить историю покупок клиента
            if ($validated['customer_id']) {
                $bonusEarned = floor($finalAmount / 100); // 1 балл за каждые 100 сом
                
                CustomerPurchaseHistory::create([
                    'customer_id' => $validated['customer_id'],
                    'transaction_id' => $transaction->id,
                    'amount' => $finalAmount,
                    'bonus_earned' => $bonusEarned,
                ]);
                
                // Обновить профиль клиента
                $customer = \Modules\Core\Models\User::find($validated['customer_id']);
                $customer->customerProfile?->updateAfterPurchase($finalAmount, $bonusEarned);
            }
            
            DB::commit();
            
            return redirect()->route('seller.receipts.show', $transaction->id)
                ->with('success', 'Продажа успешно оформлена');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ошибка при оформлении продажи: ' . $e->getMessage()]);
        }
    }
}
