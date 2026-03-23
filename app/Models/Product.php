<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'article', 'slug', 'category_id', 'description', 'brand', 'material', 'country', 'photo', 'photos', 'retail_price', 'is_active', 'created_by'];
    protected $casts = ['photos' => 'array', 'is_active' => 'boolean', 'retail_price' => 'decimal:2'];

    public function category() { return $this->belongsTo(Category::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
