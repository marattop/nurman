<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'article',
        'slug',
        'category_id',
        'description',
        'brand',
        'material',
        'country',
        'photo',
        'photos',
        'retail_price',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_active' => 'boolean',
        'retail_price' => 'decimal:2',
    ];

    /**
     * Категория товара
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Варианты товара (размеры, цвета)
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Пользователь, создавший товар
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'created_by');
    }

    /**
     * Закупки этого товара
     */
    public function purchases(): HasMany
    {
        return $this->hasManyThrough(Purchase::class, ProductVariant::class, 'product_id', 'variant_id');
    }

    /**
     * QR-коды товара
     */
    public function qrCodes(): HasMany
    {
        return $this->hasManyThrough(QrCode::class, ProductVariant::class, 'product_id', 'variant_id');
    }

    /**
     * Получить общий остаток по всем вариантам
     */
    public function getTotalStockAttribute(): int
    {
        $total = 0;
        foreach ($this->variants as $variant) {
            $total += $variant->total_stock;
        }
        return $total;
    }

    /**
     * Получить минимальную цену среди всех вариантов
     */
    public function getMinPriceAttribute(): ?float
    {
        $minPrice = $this->variants
            ->filter(fn($v) => $v->supplier_price > 0)
            ->min('supplier_price');
        
        return $minPrice ?: null;
    }

    /**
     * Получить максимальную цену среди всех вариантов
     */
    public function getMaxPriceAttribute(): ?float
    {
        $maxPrice = $this->variants->max('supplier_price');
        return $maxPrice ?: null;
    }

    /**
     * Получить URL главного фото
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset($this->photo);
        }
        return null;
    }

    /**
     * Получить все фото URLs
     */
    public function getPhotosUrlsAttribute(): array
    {
        if (!$this->photos) {
            return $this->photo ? [asset($this->photo)] : [];
        }

        return array_map(fn($p) => asset($p), $this->photos);
    }
}
