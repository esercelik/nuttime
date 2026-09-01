<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'image', 'image_alt', 'seo_title', 'seo_description', 'seo_canonical', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translationFor(string $locale): ?CategoryTranslation
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
}
