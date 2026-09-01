<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MenuItem extends Model
{
    protected $fillable = ['parent_id', 'link_type', 'route_name', 'url', 'open_in_new_tab', 'is_active', 'sort_order'];

    protected $casts = ['open_in_new_tab' => 'boolean', 'is_active' => 'boolean'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function translationFor(string $locale): ?MenuItemTranslation
    {
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();
        foreach (array_unique([$locale, config('nuttime.fallback_locale'), config('nuttime.default_locale')]) as $fallbackLocale) {
            if ($translation = $translations->firstWhere('locale', $fallbackLocale)) {
                return $translation;
            }
        }

        return null;
    }

    protected static function booted(): void
    {
        self::creating(function (self $item): void {
            if (! $item->menu_id && $item->parent_id) {
                $item->menu_id = static::query()->whereKey($item->parent_id)->value('menu_id');
            }
        });
    }
}
