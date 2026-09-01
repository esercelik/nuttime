<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PageSection extends Model
{
    use SoftDeletes;

    private const SETTINGS = [
        'banner' => ['overlay'],
        'featured_products' => ['limit'],
        'categories' => ['limit'],
        'social' => ['network'],
        'custom' => ['variant'],
    ];

    protected $fillable = ['page_key', 'key', 'type', 'status', 'is_active', 'sort_order', 'desktop_image', 'mobile_image', 'video_url', 'background_color', 'text_color', 'accent_color', 'variant', 'settings', 'published_from', 'published_until'];

    protected $casts = ['is_active' => 'boolean', 'settings' => 'array', 'published_from' => 'datetime', 'published_until' => 'datetime'];

    public function translations(): HasMany
    {
        return $this->hasMany(PageSectionTranslation::class);
    }

    public function translationFor(string $locale): ?PageSectionTranslation
    {
        $translations = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();

        foreach (array_unique([$locale, config('nuttime.fallback_locale'), config('nuttime.default_locale')]) as $fallbackLocale) {
            if ($translation = $translations->firstWhere('locale', $fallbackLocale)) {
                return $translation;
            }
        }

        return null;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('published_from')->orWhere('published_from', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('published_until')->orWhere('published_until', '>=', now()));
    }

    /** @return array<string, bool|int|string> */
    public function safeSettings(): array
    {
        $allowed = self::SETTINGS[$this->type] ?? [];
        $settings = is_array($this->settings) ? $this->settings : [];

        return collect($settings)->only($allowed)->mapWithKeys(function (mixed $value, string $key): array {
            return match ($key) {
                'limit' => [$key => min(max((int) $value, 1), 12)],
                'overlay' => [$key => (bool) $value],
                'network' => [$key => in_array($value, ['instagram', 'facebook', 'youtube'], true) ? $value : 'instagram'],
                'variant' => [$key => in_array($value, ['editorial', 'compact'], true) ? $value : 'editorial'],
            };
        })->all();
    }

    /** @param array<string, mixed>|null $value */
    public function setSettingsAttribute(?array $value): void
    {
        $allowed = self::SETTINGS[$this->attributes['type'] ?? $this->type ?? ''] ?? [];
        $this->attributes['settings'] = json_encode(collect($value ?? [])->only($allowed)->all(), JSON_THROW_ON_ERROR);
    }
}
