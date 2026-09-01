<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\PageSection;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;

final class CmsInitialContentSeeder
{
    public function __construct(private CmsContentRepository $cmsContent) {}

    /** @return array{menus: int, sections: int, sliderItems: int} */
    public function seed(): array
    {
        return DB::transaction(function (): array {
            $menus = $this->seedMenus();
            $sections = $this->seedHomeSections();
            $sliderItems = $this->seedHomeSlider();

            $this->cmsContent->forget();

            return compact('menus', 'sections', 'sliderItems');
        });
    }

    private function seedMenus(): int
    {
        $definitions = [
            'header-primary' => ['name' => 'Ana navigasyon', 'location' => 'header', 'items' => ['home', 'products', 'about', 'certificates']],
            'footer-primary' => ['name' => 'Footer navigasyon', 'location' => 'footer', 'items' => ['products', 'about', 'certificates']],
            'footer-legal' => ['name' => 'Footer yasal', 'location' => 'legal', 'items' => ['contact']],
        ];
        $created = 0;

        foreach ($definitions as $key => $definition) {
            if (Menu::query()->where('key', $key)->exists()) {
                continue;
            }

            $menu = Menu::query()->create(['key' => $key, 'name' => $definition['name'], 'location' => $definition['location'], 'is_active' => true]);
            foreach ($definition['items'] as $index => $routeName) {
                $item = $menu->items()->create(['link_type' => 'internal', 'route_name' => $routeName, 'is_active' => true, 'sort_order' => $index]);
                foreach (array_keys(config('nuttime.locales')) as $locale) {
                    $item->translations()->create(['locale' => $locale, 'label' => __('site.nav.'.$routeName, [], $locale)]);
                }
            }
            $created++;
        }

        return $created;
    }

    private function seedHomeSections(): int
    {
        if (PageSection::query()->where('page_key', 'home')->exists()) {
            return 0;
        }

        $definitions = [
            ['key' => 'intro', 'type' => 'intro', 'translation' => ['eyebrow' => 'NUTTIME', 'title' => 'site.home.intro']],
            ['key' => 'featured_products', 'type' => 'featured_products', 'translation' => ['eyebrow' => 'site.home.featured_kicker', 'title' => 'site.home.featured_title']],
            ['key' => 'categories', 'type' => 'categories', 'translation' => ['eyebrow' => 'site.home.categories_kicker', 'title' => 'site.home.categories_title']],
            ['key' => 'story', 'type' => 'story', 'translation' => ['eyebrow' => 'site.home.story_kicker', 'title' => 'site.home.story_title', 'description' => 'site.home.story_copy']],
            ['key' => 'certificates', 'type' => 'certificates', 'translation' => ['eyebrow' => 'site.home.quality_kicker', 'title' => 'site.home.quality_title']],
            ['key' => 'factory', 'type' => 'factory', 'translation' => []],
            ['key' => 'social', 'type' => 'social', 'translation' => ['eyebrow' => 'site.home.social_kicker', 'title' => 'site.home.social_title'], 'settings' => ['network' => 'instagram']],
            ['key' => 'cta', 'type' => 'cta', 'translation' => ['eyebrow' => 'site.final_cta.kicker', 'title' => 'site.final_cta.title']],
        ];

        foreach ($definitions as $order => $definition) {
            $section = PageSection::query()->create(['page_key' => 'home', 'key' => $definition['key'], 'type' => $definition['type'], 'status' => 'published', 'is_active' => true, 'sort_order' => $order, 'settings' => $definition['settings'] ?? []]);
            foreach (array_keys(config('nuttime.locales')) as $locale) {
                $translation = $definition['translation'];
                $section->translations()->create([
                    'locale' => $locale,
                    'eyebrow' => str_starts_with($translation['eyebrow'] ?? '', 'site.') ? __($translation['eyebrow'], [], $locale) : ($translation['eyebrow'] ?? null),
                    'title' => str_starts_with($translation['title'] ?? '', 'site.') ? __($translation['title'], [], $locale) : ($translation['title'] ?? null),
                    'description' => str_starts_with($translation['description'] ?? '', 'site.') ? __($translation['description'], [], $locale) : ($translation['description'] ?? null),
                ]);
            }
        }

        return count($definitions);
    }

    private function seedHomeSlider(): int
    {
        if (Slider::query()->where('key', 'home')->exists()) {
            return 0;
        }

        $products = Product::query()->active()->with('translations')->orderBy('sort_order')->limit(6)->get();
        if ($products->isEmpty()) {
            return 0;
        }

        $slider = Slider::query()->create(['key' => 'home', 'name' => 'Ana sayfa sliderı', 'status' => 'published', 'is_active' => true, 'settings' => ['autoplay_ms' => 6500, 'loop' => true, 'show_arrows' => true, 'show_counter' => true, 'show_progress' => true, 'swipe' => true]]);
        foreach ($products as $order => $product) {
            $item = $slider->items()->create(['product_id' => $product->id, 'status' => 'published', 'is_active' => true, 'sort_order' => $order]);
            foreach (array_keys(config('nuttime.locales')) as $locale) {
                $translation = $product->translations->firstWhere('locale', $locale);
                $item->translations()->create(['locale' => $locale, 'title' => $translation?->name ?? $product->name, 'description' => $translation?->short_description ?? $product->short_description]);
            }
        }

        return $products->count();
    }
}
