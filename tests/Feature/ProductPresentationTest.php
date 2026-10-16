<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Support\InitialCatalogImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class ProductPresentationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_product_detail_renders_real_packaging_and_nutrition_data_when_available(): void
    {
        $product = Product::factory()->create([
            'name' => 'Pâte de noisettes',
            'slug' => 'pate-de-noisettes-test',
            'short_description' => 'Description du produit.',
            'weight_grams' => 250,
            'feature_tags' => ['Şeker ilavesiz', 'Vegan'],
            'packaging_details' => ['jar.net_weight' => '250 gr'],
            'nutrition_facts' => ['energy' => '600 kcal'],
        ]);
        $product->translations()->create([
            'locale' => 'fr',
            'name' => 'Pâte de noisettes',
            'slug' => 'pate-de-noisettes-test',
            'ingredients' => 'Noisettes (45 %), sucre.',
            'allergen_information' => 'Contient des noisettes.',
        ]);

        $this->get(route('site.fr.product', ['slug' => $product->slug]))
            ->assertSee('Pâte de noisettes')
            ->assertSee('250 g')
            ->assertSee('Şeker ilavesiz · Vegan')
            ->assertSee('Noisettes (45 %), sucre.')
            ->assertSee('Informations allergènes')
            ->assertSee('250 gr')
            ->assertSee('600 kcal');
    }

    public function test_peanut_butter_fallback_renders_the_structured_source_data(): void
    {
        app(InitialCatalogImporter::class)->import();

        $this->get(route('site.fr.product', ['slug' => 'beurre-de-cacahuete']))
            ->assertOk()
            ->assertSee('Cacahuètes (52 %), sucre de betterave')
            ->assertSee('Informations allergènes')
            ->assertSee('556 / 2337')
            ->assertSee('Pot')
            ->assertSee('Palette de cartons')
            ->assertSee('Seau industriel / Europalette')
            ->assertSee('EUP (Europalette)')
            ->assertSee('326,0 mm')
            ->assertSee('images/nuttime/products/peanut/jar.jpg', false)
            ->assertSee('images/nuttime/products/peanut/detail.jpg', false)
            ->assertSee('images/nuttime/products/peanut/lifestyle.jpg', false)
            ->assertSee('count: 5', false)
            ->assertSee('product-stage__gallery-controls', false)
            ->assertSee('role="group"', false);
    }

    public function test_pistachio_butter_fallback_renders_the_structured_source_data(): void
    {
        app(InitialCatalogImporter::class)->import();

        $this->get(route('site.fr.product', ['slug' => 'pate-de-pistache']))
            ->assertOk()
            ->assertSee('Pistaches (42 %), sucre de betterave')
            ->assertSee('Informations allergènes')
            ->assertSee('560 / 2344')
            ->assertSee('Pot')
            ->assertSee('Carton')
            ->assertSee('Palette de cartons')
            ->assertSee('Seau industriel / Europalette')
            ->assertSee('100')
            ->assertSee('24')
            ->assertSee('255 × 325 × 115 mm')
            ->assertSee('326,0 mm')
            ->assertSee('images/nuttime/products/pistachio/jar.jpg', false)
            ->assertSee('images/nuttime/products/pistachio/spoon.jpg', false);

        $this->get(route('site.fr.product', ['slug' => 'pate-de-pistache']))
            ->assertDontSee('BUNLARI DA KEŞFEDİN')
            ->assertDontSee('Seçkiden');

        $this->get(route('site.en.product', ['slug' => 'pistachio-butter']))
            ->assertOk()
            ->assertSee('Pistachio Butter')
            ->assertSee('Ingredients')
            ->assertSee('Carton pallet')
            ->assertSee('Industrial pail / Europallet');
    }

    public function test_remaining_product_fallbacks_render_their_source_specific_details(): void
    {
        app(InitialCatalogImporter::class)->import();

        $products = [
            ['slug' => 'pate-de-noisettes', 'ingredients' => 'Noisettes (45 %), sucre de betterave', 'energy' => '544 / 2277'],
            ['slug' => 'pate-d-amandes', 'ingredients' => 'Amandes (45 %), sucre de betterave', 'energy' => '561 / 2348'],
            ['slug' => 'beurre-de-cacahuete-sans-sucre-ajoute', 'ingredients' => 'Cacahuètes (72 %), lait entier en poudre', 'energy' => '570 / 2306'],
            ['slug' => 'creme-de-coco', 'ingredients' => 'Noix de coco (42 %), sucre de betterave', 'energy' => '567 / 2773'],
        ];

        foreach ($products as $product) {
            $this->get(route('site.fr.product', ['slug' => $product['slug']]))
                ->assertOk()
                ->assertSee($product['ingredients'])
                ->assertSee($product['energy'])
                ->assertSee('Palette de cartons')
                ->assertSee('Seau industriel / Europalette')
                ->assertSee('326,0 mm');
        }
    }

    public function test_product_detail_uses_content_fallbacks_when_optional_seo_values_are_null(): void
    {
        $product = Product::factory()->create([
            'name' => 'Fallback product',
            'slug' => 'fallback-product',
            'short_description' => 'Fallback description.',
            'seo_title' => null,
            'seo_description' => null,
            'seo_canonical' => null,
        ]);
        $product->translations()->createMany([
            ['locale' => 'fr', 'name' => 'Produit sans titre SEO', 'slug' => 'produit-sans-titre-seo'],
            ['locale' => 'en', 'name' => 'Product Without SEO Title', 'slug' => 'product-without-seo-title'],
        ]);

        $this->get(route('site.fr.product', ['slug' => 'produit-sans-titre-seo']))
            ->assertOk()
            ->assertSee('<title>Produit sans titre SEO | Nuttime</title>', false)
            ->assertSee('Fallback description.');

        $this->get(route('site.en.product', ['slug' => 'product-without-seo-title']))
            ->assertOk()
            ->assertSee('<title>Product Without SEO Title | Nuttime</title>', false)
            ->assertSee('Fallback description.');
    }

    public function test_all_fallback_product_detail_routes_render_in_french_and_english(): void
    {
        app(InitialCatalogImporter::class)->import();

        $products = [
            ['fr' => 'pate-de-noisettes', 'en' => 'hazelnut-butter'],
            ['fr' => 'pate-de-pistache', 'en' => 'pistachio-butter'],
            ['fr' => 'pate-d-amandes', 'en' => 'almond-butter'],
            ['fr' => 'beurre-de-cacahuete', 'en' => 'peanut-butter'],
            ['fr' => 'beurre-de-cacahuete-sans-sucre-ajoute', 'en' => 'no-added-sugar-peanut-butter'],
            ['fr' => 'creme-de-coco', 'en' => 'coconut-butter'],
        ];

        foreach ($products as $product) {
            $this->get(route('site.fr.product', ['slug' => $product['fr']]))->assertOk();
            $this->get(route('site.en.product', ['slug' => $product['en']]))->assertOk();
        }
    }

    public function test_whole_ingredient_percentage_keeps_its_trailing_zeroes(): void
    {
        $product = Product::factory()->create(['primary_ingredient_percentage' => 100, 'weight_grams' => 250]);

        $this->get(route('site.fr.product', ['slug' => $product->slug]))->assertOk()
            ->assertSee('100%')
            ->assertSee('Poids net');
    }
}
