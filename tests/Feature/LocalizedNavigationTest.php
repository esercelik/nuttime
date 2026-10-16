<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\InitialCatalogImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class LocalizedNavigationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_language_picker_renders_only_french_german_and_english(): void
    {
        $response = $this->get(route('site.fr.home'));

        $response
            ->assertSee('Français')
            ->assertSee('English')
            ->assertSee('Deutsch')
            ->assertDontSee('Türkçe')
            ->assertDontSee('Español');
    }

    public function test_footer_renders_the_nuttime_instagram_link_and_studio_credit(): void
    {
        $response = $this->get(route('site.fr.home'));

        $response
            ->assertSee('href="https://www.instagram.com/nuttimetr/" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('Site by <strong>Çelik Studio</strong>', false);
    }

    public function test_supported_locales_keep_product_pages_and_alternates_available(): void
    {
        app(InitialCatalogImporter::class)->import();
        $product = Product::query()->with('translations')->where('slug', 'findik-kremasi')->firstOrFail();

        foreach (['fr', 'de', 'en'] as $locale) {
            $slug = $product->translationFor($locale)?->slug ?: $product->slug;
            $response = $this->get(route('site.'.$locale.'.product', ['slug' => $slug]));

            $response
                ->assertOk()
                ->assertSee('hreflang="'.$locale.'"', false)
                ->assertSee(route('site.fr.product', ['slug' => $product->translationFor('fr')?->slug ?: $product->slug]), false);
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

    public function test_french_product_page_sets_french_document_language(): void
    {
        app(InitialCatalogImporter::class)->import();

        $this->get(route('site.fr.product', ['slug' => 'pate-de-noisettes']))
            ->assertSee('<html lang="fr-FR" dir="ltr">', false)
            ->assertSee('Français');
    }

    public function test_initial_catalog_contains_each_supported_product_translation(): void
    {
        app(InitialCatalogImporter::class)->import();

        $product = Product::query()->with('translations')->where('slug', 'findik-kremasi')->firstOrFail();

        $supportedLocales = array_keys(config('nuttime.locales'));
        sort($supportedLocales);

        $this->assertSame($supportedLocales, $product->translations->pluck('locale')->sort()->values()->all());
    }

    public function test_catalog_import_preserves_historical_translations(): void
    {
        app(InitialCatalogImporter::class)->import();
        $product = Product::query()->where('slug', 'findik-kremasi')->firstOrFail();
        $product->translations()->create(['locale' => 'tr', 'name' => 'Fındık Ezmesi', 'slug' => 'findik-kremasi']);

        app(InitialCatalogImporter::class)->import();

        $this->assertSame(4, $product->translations()->count());
        $this->assertSame('Fındık Ezmesi', $product->translations()->where('locale', 'tr')->firstOrFail()->name);
    }

    public function test_product_detail_uses_the_selected_locale_translation_from_the_database(): void
    {
        app(InitialCatalogImporter::class)->import();

        $response = $this->get(route('site.fr.product', ['slug' => 'pate-de-noisettes']));

        $response
            ->assertSee('Pâte de noisettes')
            ->assertSee('Ingrédients')
            ->assertSee('href="'.route('site.de.product', ['slug' => 'haselnusscreme']).'"', false);
    }
}
