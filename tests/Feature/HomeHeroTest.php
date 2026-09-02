<?php

namespace Tests\Feature;

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
}
