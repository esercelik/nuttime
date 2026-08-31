<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'main_image', 'additional_images', 'price', 'compare_price', 'stock', 'stock_tracking', 'is_active', 'is_featured', 'sort_order', 'seo_title', 'seo_description'];

    protected $casts = ['additional_images' => 'array', 'stock_tracking' => 'boolean', 'is_active' => 'boolean', 'is_featured' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
