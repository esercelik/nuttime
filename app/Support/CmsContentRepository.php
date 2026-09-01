<?php

namespace App\Support;

use App\Models\PageSection;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;

final class CmsContentRepository
{
    public function __construct(private LocalizedContent $localizedContent, private LocalizedUrl $localizedUrl) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function homeSections(string $locale): array
    {
        return Cache::remember('cms.home.sections.'.$locale, now()->addMinutes(10), function () use ($locale): array {
            return PageSection::query()->published()->where('page_key', 'home')->with('translations')->orderBy('sort_order')->orderBy('id')->get()
                ->map(function (PageSection $section) use ($locale): array {
                    $translation = $section->translationFor($locale);

                    return [
                        'key' => $section->key, 'type' => $section->type, 'variant' => $section->variant,
                        'eyebrow' => $translation?->eyebrow, 'title' => $translation?->title, 'description' => $translation?->description,
                        'button_label' => $translation?->button_label, 'button_url' => $translation?->button_url,
                        'desktop_image' => $this->assetUrl($section->desktop_image), 'mobile_image' => $this->assetUrl($section->mobile_image),
                        'video_url' => $section->video_url, 'background_color' => $section->background_color, 'text_color' => $section->text_color,
                        'accent_color' => $section->accent_color, 'settings' => $section->settings ?? [],
                    ];
                })->all();
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function homeSlider(string $locale): array
    {
        return Cache::remember('cms.home.slider.'.$locale, now()->addMinutes(10), function () use ($locale): array {
            $slider = Slider::query()->where('key', 'home')->where('status', 'published')->where('is_active', true)
                ->with(['items' => fn ($query) => $query->published()->with(['translations', 'product.category.translations', 'product.translations'])->orderBy('sort_order')])->first();

            if (! $slider) {
                return [];
            }

            return $slider->items->map(function ($item) use ($locale): array {
                $translation = $item->translationFor($locale);
                $product = $item->product ? $this->localizedContent->product($item->product, $locale) : null;
                $slug = $product['slug'] ?? null;

                return [
                    'name' => $translation?->title ?: $product['name'] ?? '',
                    'category' => $translation?->eyebrow ?: $product['category'] ?? '',
                    'description' => $translation?->description ?: $product['description'] ?? '',
                    'url' => $translation?->cta_url ?: ($slug ? $this->localizedUrl->route('product', $locale, ['slug' => $slug]) : $this->localizedUrl->route('products', $locale)),
                    'cta_label' => $translation?->cta_label,
                    'background_image' => $this->assetUrl($item->background_image) ?: $product['image'] ?? '',
                    'mobile_background_image' => $this->assetUrl($item->mobile_background_image),
                    'ingredient_image' => $this->assetUrl($item->decoration_image) ?: $product['image'] ?? '',
                    'mobile_ingredient_image' => $this->assetUrl($item->mobile_decoration_image),
                    'product_image' => $this->assetUrl($item->product_image) ?: $product['image'] ?? '',
                    'background_color' => $item->background_color, 'text_color' => $item->text_color, 'accent_color' => $item->accent_color,
                ];
            })->all();
        });
    }

    public function forget(): void
    {
        foreach (array_keys(config('nuttime.locales')) as $locale) {
            Cache::forget('cms.home.sections.'.$locale);
            Cache::forget('cms.home.slider.'.$locale);
        }
    }

    private function assetUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('storage/'.$path);
    }
}
