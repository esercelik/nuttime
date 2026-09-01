<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Menu;
use App\Models\PageSection;
use App\Models\Slider;
use App\Support\CmsContentRepository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class CmsContentRepositoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_slider_uses_the_locale_fallback_and_only_published_items(): void
    {
        $slider = Slider::query()->create([
            'key' => 'home',
            'name' => 'Ana Sayfa Sliderı',
            'status' => 'published',
            'is_active' => true,
        ]);
        $publishedItem = $slider->items()->create([
            'status' => 'published',
            'is_active' => true,
            'sort_order' => 1,
            'background_image' => 'sliders/background.png',
            'product_image' => 'sliders/product.png',
        ]);
        $publishedItem->translations()->create([
            'locale' => 'tr',
            'title' => 'Yönetilen başlık',
            'description' => 'Yönetilen açıklama',
        ]);
        $slider->items()->create([
            'status' => 'draft',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        app(CmsContentRepository::class)->forget();
        $slides = app(CmsContentRepository::class)->homeSlider('de');

        $this->assertCount(1, $slides);
        $this->assertSame('Yönetilen başlık', $slides[0]['name']);
        $this->assertSame('Yönetilen açıklama', $slides[0]['description']);
    }

    public function test_section_changes_create_an_audit_entry(): void
    {
        $section = PageSection::query()->create([
            'page_key' => 'home',
            'key' => 'banner_one',
            'type' => 'banner',
            'status' => 'draft',
            'is_active' => true,
        ]);

        $section->update(['status' => 'published']);

        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => PageSection::class,
            'auditable_id' => $section->id,
            'event' => 'updated',
        ]);
        $this->assertSame(2, AuditLog::query()->whereMorphedTo('auditable', $section)->count());
    }

    public function test_menu_uses_translated_active_items_and_localized_routes(): void
    {
        $menu = Menu::query()->create([
            'key' => 'header-primary',
            'name' => 'Ana navigasyon',
            'is_active' => true,
        ]);
        $item = $menu->items()->create([
            'link_type' => 'internal',
            'route_name' => 'products',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $item->translations()->create([
            'locale' => 'en',
            'label' => 'Shop',
        ]);
        $menu->items()->create([
            'link_type' => 'internal',
            'route_name' => 'about',
            'is_active' => false,
        ]);

        app(CmsContentRepository::class)->forget();

        $menuItems = app(CmsContentRepository::class)->menu('header-primary', 'en');

        $this->assertSame([[
            'label' => 'Shop',
            'url' => route('site.en.products'),
            'new_tab' => false,
            'children' => [],
        ]], $menuItems);
    }
}
