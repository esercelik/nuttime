<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'previous_slugs', 'sku', 'short_description', 'description', 'main_image', 'main_image_alt', 'additional_images', 'price', 'compare_price', 'stock', 'stock_tracking', 'is_active', 'is_featured', 'sort_order', 'seo_title', 'seo_description', 'seo_canonical'];

    protected $casts = ['additional_images' => 'array', 'previous_slugs' => 'array', 'stock_tracking' => 'boolean', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'price' => 'decimal:2'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function translationFor(string $locale): ?ProductTranslation
    {
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();

        foreach (array_unique([$locale, config('nuttime.fallback_locale'), config('nuttime.default_locale')]) as $fallbackLocale) {
            $translation = $translations->firstWhere('locale', $fallbackLocale);

            if ($translation) {
                return $translation;
            }
        }

        return null;
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }
}
