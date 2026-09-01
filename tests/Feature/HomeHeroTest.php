<?php

namespace Tests\Feature;

use Tests\TestCase;

final class HomeHeroTest extends TestCase
{
    public function test_home_renders_the_fullscreen_product_slider_with_banners_afterward(): void
    {
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
}
