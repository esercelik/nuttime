<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'previous_slugs', 'sku', 'short_description', 'description', 'main_image', 'main_image_alt', 'additional_images', 'price', 'compare_price', 'stock', 'stock_tracking', 'is_active', 'is_featured', 'sort_order', 'seo_title', 'seo_description', 'seo_canonical'];

    protected $casts = ['additional_images' => 'array', 'previous_slugs' => 'array', 'stock_tracking' => 'boolean', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'price' => 'decimal:2'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }
}
