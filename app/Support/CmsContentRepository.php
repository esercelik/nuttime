<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\PageSection;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

final class CmsContentRepository
{
    private const HOME_SECTION_TYPES = ['banner', 'intro', 'featured_products', 'categories', 'story', 'certificates', 'factory', 'social', 'cta', 'custom'];

    public function __construct(private LocalizedContent $localizedContent, private LocalizedUrl $localizedUrl) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function homeSections(string $locale): array
    {
        if (! Schema::hasTable('page_sections')) {
            return [];
        }

        return Cache::remember('cms.home.sections.'.$locale, now()->addMinutes(10), function () use ($locale): array {
            return PageSection::query()->published()->where('page_key', 'home')->whereIn('type', self::HOME_SECTION_TYPES)->with('translations')->orderBy('sort_order')->orderBy('id')->get()
                ->map(function (PageSection $section) use ($locale): array {
                    $translation = $section->translationFor($locale);

                    return [
                        'key' => $section->key, 'type' => $section->type, 'variant' => $section->variant,
                        'eyebrow' => $translation?->eyebrow, 'title' => $translation?->title, 'description' => $translation?->description,
                        'button_label' => $translation?->button_label, 'button_url' => $translation?->button_url,
                        'desktop_image' => $this->assetUrl($section->desktop_image), 'mobile_image' => $this->assetUrl($section->mobile_image),
                        'video_url' => $section->video_url, 'background_color' => $section->background_color, 'text_color' => $section->text_color,
                        'accent_color' => $section->accent_color, 'settings' => $section->safeSettings(),
                    ];
                })->all();
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function homeSlider(string $locale): array
    {
        if (! Schema::hasTable('sliders') || ! Schema::hasTable('slider_items')) {
            return [];
        }

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

    /** @return array{autoplay_ms: int, loop: bool, show_arrows: bool, show_counter: bool, show_progress: bool, swipe: bool} */
    public function homeSliderSettings(): array
    {
        if (! Schema::hasTable('sliders')) {
            return $this->defaultHomeSliderSettings();
        }

        return Cache::remember('cms.home.slider.settings', now()->addMinutes(10), function (): array {
            $settings = Slider::query()->where('key', 'home')->where('status', 'published')->where('is_active', true)->value('settings');

            return $this->normalizeHomeSliderSettings(is_array($settings) ? $settings : []);
        });
    }

    public function forget(): void
    {
        $this->forgetHomeSections();
        $this->forgetHomeSlider();
        $this->forgetMenus();
    }

    public function forgetHomeSections(): void
    {
        foreach (array_keys(config('nuttime.locales')) as $locale) {
            Cache::forget('cms.home.sections.'.$locale);
        }
    }

    public function forgetHomeSlider(): void
    {
        Cache::forget('cms.home.slider.settings');

        foreach (array_keys(config('nuttime.locales')) as $locale) {
            Cache::forget('cms.home.slider.'.$locale);
        }
    }

    public function forgetMenus(): void
    {
        foreach (array_keys(config('nuttime.locales')) as $locale) {
            Cache::forget('cms.menu.header-primary.'.$locale);
            Cache::forget('cms.menu.footer-primary.'.$locale);
            Cache::forget('cms.menu.footer-legal.'.$locale);
        }
    }

    /**
     * @return array<int, array{label: string, url: string, new_tab: bool, children: array<int, array{label: string, url: string, new_tab: bool, children: array}>}>
     */
    public function menu(string $key, string $locale): array
    {
        if (! Schema::hasTable('menus') || ! Schema::hasTable('menu_items')) {
            return [];
        }

        return Cache::remember('cms.menu.'.$key.'.'.$locale, now()->addMinutes(10), function () use ($key, $locale): array {
            $menu = Menu::query()
                ->where('key', $key)
                ->where('is_active', true)
                ->with(['items' => fn ($query) => $query->whereNull('parent_id')->where('is_active', true)->with('translations', 'children.translations', 'children.children.translations')->orderBy('sort_order')])
                ->first();

            if (! $menu) {
                return [];
            }

            return $menu->items->map(fn (MenuItem $item): ?array => $this->menuItem($item, $locale))
                ->filter()->values()->all();
        });
    }

    /** @return array{label: string, url: string, new_tab: bool, children: array<int, array>}|null */
    private function menuItem(MenuItem $item, string $locale): ?array
    {
        $translation = $item->translationFor($locale);
        $url = $item->link_type === 'external'
            ? $this->safeExternalUrl($item->url)
            : $this->safeLocalizedRoute($item->route_name, $locale);

        if (! filled($translation?->label) || ! $url) {
            return null;
        }

        return [
            'label' => $translation->label,
            'url' => $url,
            'new_tab' => $item->link_type === 'external' && $item->open_in_new_tab,
            'children' => $item->children->where('is_active', true)->sortBy('sort_order')->map(fn (MenuItem $child): ?array => $this->menuItem($child, $locale))->filter()->values()->all(),
        ];
    }

    private function safeLocalizedRoute(?string $routeName, string $locale): ?string
    {
        if (! filled($routeName) || ! Route::has('site.'.$locale.'.'.$routeName)) {
            return null;
        }

        return $this->localizedUrl->route($routeName, $locale);
    }

    private function safeExternalUrl(?string $url): ?string
    {
        return filter_var($url, FILTER_VALIDATE_URL) && str_starts_with((string) $url, 'https://') ? $url : null;
    }

    /** @return array{autoplay_ms: int, loop: bool, show_arrows: bool, show_counter: bool, show_progress: bool, swipe: bool} */
    private function defaultHomeSliderSettings(): array
    {
        return ['autoplay_ms' => 6500, 'loop' => true, 'show_arrows' => true, 'show_counter' => true, 'show_progress' => true, 'swipe' => true];
    }

    /** @param array<string, mixed> $settings @return array{autoplay_ms: int, loop: bool, show_arrows: bool, show_counter: bool, show_progress: bool, swipe: bool} */
    private function normalizeHomeSliderSettings(array $settings): array
    {
        $defaults = $this->defaultHomeSliderSettings();
        $autoplay = filter_var($settings['autoplay_ms'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 30000]]);

        return [
            'autoplay_ms' => $autoplay ?: $defaults['autoplay_ms'],
            'loop' => filter_var($settings['loop'] ?? $defaults['loop'], FILTER_VALIDATE_BOOLEAN),
            'show_arrows' => filter_var($settings['show_arrows'] ?? $defaults['show_arrows'], FILTER_VALIDATE_BOOLEAN),
            'show_counter' => filter_var($settings['show_counter'] ?? $defaults['show_counter'], FILTER_VALIDATE_BOOLEAN),
            'show_progress' => filter_var($settings['show_progress'] ?? $defaults['show_progress'], FILTER_VALIDATE_BOOLEAN),
            'swipe' => filter_var($settings['swipe'] ?? $defaults['swipe'], FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('storage/'.$path);
    }
}
