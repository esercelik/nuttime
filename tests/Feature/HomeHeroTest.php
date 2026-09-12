<?php

namespace Tests\Feature;

use App\Models\PageSection;
use App\Support\CmsInitialContentSeeder;
use App\Support\InitialCatalogImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class HomeHeroTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_renders_the_fullscreen_product_slider_with_banners_afterward(): void
    {
        app(InitialCatalogImporter::class)->import();

        $response = $this->get(route('site.tr.home'));

        $response->assertSee('FINDIĞIN KAVRULMUŞ ZENGİNLİĞİ')
            ->assertSee('Ürünü incele')
            ->assertSee('data-product-hero-background', false)
            ->assertSee('data-product-hero-jar', false)
            ->assertSee('data-autoplay="6500"', false)
            ->assertSee('data-product-hero-pagination="0"', false)
            ->assertSee('data-product-hero-pagination="5"', false)
            ->assertSee('data-product-hero-previous', false)
            ->assertSee('data-product-hero-next', false)
            ->assertSee('id="home-banners"', false)
            ->assertSee('/images/nuttime/spylt/nuttime-hindistan-cevizi-hero-background.png', false)
            ->assertSee('/images/nuttime/spylt/nuttime-antep-ingredient-elements-transparent.png', false)
            ->assertSee(route('site.tr.product', ['slug' => 'antep-fistikli-kremasi']), false)
            ->assertSee(route('site.tr.product', ['slug' => 'findik-kremasi']), false)
            ->assertSee(route('site.tr.product', ['slug' => 'yer-fistigi-ezmesi']), false)
            ->assertSee(route('site.tr.products'), false);
    }

    public function test_product_slider_keeps_localized_product_links(): void
    {
        app(InitialCatalogImporter::class)->import();

        $this->get(route('site.en.home'))
            ->assertSee('data-product-hero', false)
            ->assertSee(route('site.en.product', ['slug' => 'hazelnut-butter']), false)
            ->assertSee(route('site.en.product', ['slug' => 'pistachio-butter']), false)
            ->assertSee(route('site.en.product', ['slug' => 'almond-butter']), false);
    }

    public function test_seeded_cms_sections_keep_home_photography_when_media_is_empty(): void
    {
        app(InitialCatalogImporter::class)->import();
        app(CmsInitialContentSeeder::class)->seed();

        $this->get(route('site.en.home'))->assertOk()
            ->assertSee('images/nuttime/collection-banner.jpg', false)
            ->assertSee('images/nuttime/spread-banner.jpg', false)
            ->assertSee('images/nuttime/brand-story.jpg', false)
            ->assertSee('catalog-card__media--cutout', false)
            ->assertSee('Good products,')
            ->assertSee('id="home-banners"', false)
            ->assertDontSee('class="quality-rail', false);
    }

    public function test_home_sections_keep_custom_cms_images_and_copy(): void
    {
        foreach (['story', 'cta'] as $type) {
            $section = PageSection::query()->create(['page_key' => 'home', 'key' => $type, 'type' => $type, 'status' => 'published', 'is_active' => true, 'desktop_image' => 'media/'.$type.'.jpg']);
            $section->translations()->create(['locale' => 'en', 'title' => 'Custom '.$type, 'button_label' => 'Read '.$type, 'button_url' => '/en/contact']);
        }

        $this->get(route('site.en.home'))->assertOk()
            ->assertSee('/storage/media/story.jpg', false)
            ->assertSee('/storage/media/cta.jpg', false)
            ->assertSee('Custom story')
            ->assertSee('Custom cta')
            ->assertSee('Read cta')
            ->assertDontSee('images/nuttime/brand-story.jpg', false);
    }
}
