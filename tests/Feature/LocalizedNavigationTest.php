<?php

namespace Tests\Feature;

use Tests\TestCase;

final class LocalizedNavigationTest extends TestCase
{
    public function test_language_picker_renders_all_ten_supported_languages(): void
    {
        $response = $this->get(route('site.tr.home'));

        $response
            ->assertSee('Türkçe')
            ->assertSee('English')
            ->assertSee('Deutsch')
            ->assertSee('Français')
            ->assertSee('Español')
            ->assertSee('Italiano')
            ->assertSee('Русский')
            ->assertSee('العربية')
            ->assertSee('中文')
            ->assertSee('Português');
    }

    public function test_new_locales_keep_product_pages_available_with_the_existing_content_fallback(): void
    {
        foreach (['fr', 'es', 'it', 'ru', 'ar', 'zh', 'pt'] as $locale) {
            $response = $this->get(route('site.'.$locale.'.product', ['slug' => 'hazelnut-butter']));

            $response
                ->assertOk()
                ->assertSee('hreflang="'.$locale.'"', false)
                ->assertSee(route('site.fr.product', ['slug' => 'hazelnut-butter']), false);
        }
    }

    public function test_english_homepage_renders_localized_navigation_and_links(): void
    {
        $response = $this->get('/en/');

        $response->assertSee('Home')
            ->assertSee('Products')
            ->assertSee('About us')
            ->assertSee('/en/products"', false)
            ->assertSee('/en/about-us"', false)
            ->assertSee('/en/contact"', false);
    }
}
