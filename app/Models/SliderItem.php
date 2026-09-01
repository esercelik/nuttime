<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class SliderItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['slider_id', 'product_id', 'status', 'is_active', 'sort_order', 'background_image', 'mobile_background_image', 'product_image', 'decoration_image', 'mobile_decoration_image', 'background_color', 'text_color', 'accent_color', 'published_from', 'published_until'];

    protected $casts = ['is_active' => 'boolean', 'published_from' => 'datetime', 'published_until' => 'datetime'];

    public function slider(): BelongsTo { return $this->belongsTo(Slider::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function translations(): HasMany { return $this->hasMany(SliderItemTranslation::class); }

    public function translationFor(string $locale): ?SliderItemTranslation
    {
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();
        foreach (array_unique([$locale, config('nuttime.fallback_locale'), config('nuttime.default_locale')]) as $fallbackLocale) {
            if ($translation = $translations->firstWhere('locale', $fallbackLocale)) return $translation;
        }
        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('published_from')->orWhere('published_from', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('published_until')->orWhere('published_until', '>=', now()));
    }
}
