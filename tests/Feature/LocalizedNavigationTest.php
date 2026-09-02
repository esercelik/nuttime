<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\InitialCatalogImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class LocalizedNavigationTest extends TestCase
{
    use LazilyRefreshDatabase;

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

    public function test_footer_renders_the_nuttime_instagram_link_and_studio_credit(): void
    {
        $response = $this->get(route('site.tr.home'));

        $response
            ->assertSee('href="https://www.instagram.com/nuttimetr/" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('Site by <strong>Çelik Studio</strong>', false);
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

    public function test_arabic_product_page_sets_rtl_document_direction(): void
    {
        $this->get(route('site.ar.product', ['slug' => 'hazelnut-butter']))
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('العربية');
    }

    public function test_initial_catalog_contains_each_supported_product_translation(): void
    {
        app(InitialCatalogImporter::class)->import();

        $product = Product::query()->with('translations')->where('slug', 'findik-kremasi')->firstOrFail();

        $supportedLocales = array_keys(config('nuttime.locales'));
        sort($supportedLocales);

        $this->assertSame($supportedLocales, $product->translations->pluck('locale')->sort()->values()->all());
    }

    public function test_product_detail_uses_the_selected_locale_translation_from_the_database(): void
    {
        app(InitialCatalogImporter::class)->import();

        $response = $this->get(route('site.fr.product', ['slug' => 'pate-de-noisettes']));

        $response
            ->assertSee('Pâte de noisettes')
            ->assertSee('Ingrédients')
            ->assertSee('href="'.route('site.ar.product', ['slug' => 'hazelnut-butter']).'"', false);
    }
}
