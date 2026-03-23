<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Stock;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Сервис управления продуктами.
 */
class ProductService
{
    /**
     * Валидация данных продукта.
     */
    public function validate(array $data, $productId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'article' => 'required|string|max:100|unique:products,article,' . ($productId ?? 'NULL'),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'retail_price' => 'required|numeric|min:0',
            'supplier_price' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];

        return Validator::make($data, $rules);
    }

    /**
     * Поиск продуктов с фильтрами.
     */
    public function search(array $filters = [])
    {
        $query = Product::with(['category', 'variants.stock', 'createdBy']);

        // Поиск по названию или артикулу
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('article', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Фильтр по категории
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Фильтр по магазину
        if (!empty($filters['shop_id'])) {
            $query->whereHas('variants', function($q) use ($filters) {
                $q->whereHas('stock', function($qs) use ($filters) {
                    $qs->where('shop_id', $filters['shop_id']);
                });
            });
        }

        // Фильтр по остаткам
        if (!empty($filters['stock_status'])) {
            $this->applyStockFilter($query, $filters['stock_status']);
        }

        // Фильтр по наличию фото
        if (isset($filters['has_photo'])) {
            if ($filters['has_photo']) {
                $query->whereNotNull('photo')->where('photo', '!=', '');
            } else {
                $query->where(function($q) {
                    $q->whereNull('photo')->orWhere('photo', '');
                });
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Применение фильтра по остаткам.
     */
    private function applyStockFilter($query, $status)
    {
        if ($status === 'low') {
            $query->whereHas('variants', function($q) {
                $q->whereHas('stock', function($qs) {
                    $qs->whereColumn('quantity', '<=', 'min_stock');
                });
            });
        } elseif ($status === 'out') {
            $query->whereHas('variants', function($q) {
                $q->whereHas('stock', function($qs) {
                    $qs->where('quantity', 0);
                });
            });
        } elseif ($status === 'in') {
            $query->whereHas('variants', function($q) {
                $q->whereHas('stock', function($qs) {
                    $qs->where('quantity', '>', 0);
                });
            });
        }
    }

    /**
     * Создание продукта.
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Загрузка фото
            $photoPath = null;
            if (!empty($data['photo'])) {
                $photoPath = $data['photo']->store('products', 'public');
            }

            // Создание продукта
            $product = Product::create([
                'name' => $data['name'],
                'article' => $data['article'],
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'retail_price' => $data['retail_price'],
                'supplier_price' => $data['supplier_price'] ?? null,
                'photo' => $photoPath,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // Создание варианта по умолчанию
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $variant = $product->variants()->create($variantData);
                    
                    // Создание остатков
                    if (!empty($variantData['stock'])) {
                        foreach ($variantData['stock'] as $stockData) {
                            $variant->stock()->create($stockData);
                        }
                    }
                }
            }

            DB::commit();

            $this->logActivity($product, 'create', "Создан товар {$product->name}");

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Обновление продукта.
     */
    public function update(Product $product, array $data)
    {
        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $data['name'],
                'article' => $data['article'],
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'retail_price' => $data['retail_price'],
                'supplier_price' => $data['supplier_price'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ];

            // Загрузка нового фото
            if (!empty($data['photo'])) {
                // Удаление старого фото
                if ($product->photo) {
                    \Storage::disk('public')->delete($product->photo);
                }
                $updateData['photo'] = $data['photo']->store('products', 'public');
            }

            $product->update($updateData);

            // Обновление вариантов
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    if (!empty($variantData['id'])) {
                        $variant = ProductVariant::findOrFail($variantData['id']);
                        $variant->update($variantData);
                    } else {
                        $variant = $product->variants()->create($variantData);
                    }
                }
            }

            DB::commit();

            $this->logActivity($product, 'update', "Обновлен товар {$product->name}");

            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Удаление продукта.
     */
    public function delete(Product $product)
    {
        DB::beginTransaction();
        try {
            // Удаление фото
            if ($product->photo) {
                \Storage::disk('public')->delete($product->photo);
            }

            $name = $product->name;
            $product->delete();

            $this->logActivity($product, 'delete', "Удален товар {$name}");

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Дублирование продукта.
     */
    public function duplicate(Product $product)
    {
        DB::beginTransaction();
        try {
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Копия)';
            $newProduct->article = $product->article . '-COPY';
            $newProduct->created_by = auth()->id();
            $newProduct->save();

            // Копирование вариантов
            foreach ($product->variants as $variant) {
                $newVariant = $variant->replicate();
                $newVariant->product_id = $newProduct->id;
                $newVariant->save();

                // Копирование остатков
                foreach ($variant->stock as $stock) {
                    $newStock = $stock->replicate();
                    $newStock->variant_id = $newVariant->id;
                    $newStock->save();
                }
            }

            DB::commit();

            $this->logActivity($newProduct, 'create', "Дублирован товар {$product->name} → {$newProduct->name}");

            return $newProduct;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Логирование действий.
     */
    private function logActivity($product, $action, $description)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => "product_{$action}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
            'model_type' => Product::class,
            'model_id' => $product->id,
        ]);
    }
}
