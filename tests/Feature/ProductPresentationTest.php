<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class ProductPresentationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_product_detail_renders_real_packaging_and_nutrition_data_when_available(): void
    {
        $product = Product::factory()->create([
            'name' => 'Fındık Ezmesi',
            'slug' => 'findik-ezmesi',
            'short_description' => 'Gerçek ürün açıklaması.',
            'weight_grams' => 250,
            'feature_tags' => ['Şeker ilavesiz', 'Vegan'],
            'packaging_details' => ['jar.net_weight' => '250 gr'],
            'nutrition_facts' => ['energy' => '600 kcal'],
        ]);
        $product->translations()->create([
            'locale' => 'tr',
            'name' => 'Fındık Ezmesi',
            'slug' => 'findik-ezmesi',
            'ingredients' => 'Fındık (%45), şeker.',
            'allergen_information' => 'Fındık içerir.',
        ]);

        $this->get(route('site.tr.product', ['slug' => $product->slug]))
            ->assertSee('Fındık Ezmesi')
            ->assertSee('250 g')
            ->assertSee('Şeker ilavesiz · Vegan')
            ->assertSee('Fındık (%45), şeker.')
            ->assertSee('Alerjen bilgisi:')
            ->assertSee('250 gr')
            ->assertSee('600 kcal');
    }

    public function test_peanut_butter_fallback_renders_the_structured_source_data(): void
    {
        $this->get(route('site.tr.product', ['slug' => 'yer-fistigi-ezmesi']))
            ->assertOk()
            ->assertSee('Yer Fıstığı (%52), Pancar Şekeri')
            ->assertSee('Alerjen bilgisi:')
            ->assertSee('556 / 2337')
            ->assertSee('Kavanoz')
            ->assertSee('Koli paleti')
            ->assertSee('Endüstriyel kova / Europalette')
            ->assertSee('EUP (Europalette)')
            ->assertSee('326,0 mm')
            ->assertSee('images/nuttime/products/peanut/jar.jpg', false)
            ->assertSee('images/nuttime/products/peanut/detail.jpg', false)
            ->assertSee('images/nuttime/products/peanut/lifestyle.jpg', false)
            ->assertSee('count: 5', false)
            ->assertSee('product-stage__gallery-controls', false)
            ->assertSee('role="tablist"', false);
    }

    public function test_pistachio_butter_fallback_renders_the_structured_source_data(): void
    {
        $this->get(route('site.tr.product', ['slug' => 'antep-fistikli-kremasi']))
            ->assertOk()
            ->assertSee('Antep Fıstığı (%42), Pancar Şekeri')
            ->assertSee('Alerjen bilgisi:')
            ->assertSee('560 / 2344')
            ->assertSee('Kavanoz')
            ->assertSee('Koli')
            ->assertSee('Koli paleti')
            ->assertSee('Endüstriyel kova / Europalette')
            ->assertSee('100')
            ->assertSee('24')
            ->assertSee('255 × 325 × 115 mm')
            ->assertSee('326,0 mm')
            ->assertSee('images/nuttime/products/pistachio/jar.jpg', false)
            ->assertSee('images/nuttime/products/pistachio/spoon.jpg', false);

        $this->get(route('site.en.product', ['slug' => 'pistachio-butter']))
            ->assertOk()
            ->assertSee('Pistachio Butter')
            ->assertSee('Ingredients')
            ->assertSee('Carton pallet')
            ->assertSee('Industrial pail / Europallet');
    }

    public function test_remaining_product_fallbacks_render_their_source_specific_details(): void
    {
        $products = [
            ['slug' => 'findik-kremasi', 'ingredients' => 'Fındık (%45), Pancar Şekeri', 'energy' => '544 / 2277'],
            ['slug' => 'badem-ezmesi', 'ingredients' => 'Badem (%45), Pancar Şekeri', 'energy' => '561 / 2348'],
            ['slug' => 'seker-ilavesiz-yer-fistigi-ezmesi', 'ingredients' => 'Yer Fıstığı (%72), Yağlı Süt Tozu', 'energy' => '570 / 2306'],
            ['slug' => 'hindistan-cevizi-ezmesi', 'ingredients' => 'Hindistan Cevizi (%42), Pancar Şekeri', 'energy' => '567 / 2773'],
        ];

        foreach ($products as $product) {
            $this->get(route('site.tr.product', ['slug' => $product['slug']]))
                ->assertOk()
                ->assertSee($product['ingredients'])
                ->assertSee($product['energy'])
                ->assertSee('Koli paleti')
                ->assertSee('Endüstriyel kova / Europalette')
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
            ['locale' => 'tr', 'name' => 'SEO Başlıksız Ürün', 'slug' => 'seo-basliksiz-urun'],
            ['locale' => 'en', 'name' => 'Product Without SEO Title', 'slug' => 'product-without-seo-title'],
        ]);

        $this->get(route('site.tr.product', ['slug' => 'seo-basliksiz-urun']))
            ->assertOk()
            ->assertSee('<title>SEO Başlıksız Ürün | Nuttime</title>', false)
            ->assertSee('Fallback description.');

        $this->get(route('site.en.product', ['slug' => 'product-without-seo-title']))
            ->assertOk()
            ->assertSee('<title>Product Without SEO Title | Nuttime</title>', false)
            ->assertSee('Fallback description.');
    }

    public function test_all_fallback_product_detail_routes_render_in_turkish_and_english(): void
    {
        $products = [
            ['tr' => 'findik-kremasi', 'en' => 'hazelnut-butter'],
            ['tr' => 'antep-fistikli-kremasi', 'en' => 'pistachio-butter'],
            ['tr' => 'badem-ezmesi', 'en' => 'almond-butter'],
            ['tr' => 'yer-fistigi-ezmesi', 'en' => 'peanut-butter'],
            ['tr' => 'seker-ilavesiz-yer-fistigi-ezmesi', 'en' => 'no-added-sugar-peanut-butter'],
            ['tr' => 'hindistan-cevizi-ezmesi', 'en' => 'coconut-butter'],
        ];

        foreach ($products as $product) {
            $this->get(route('site.tr.product', ['slug' => $product['tr']]))->assertOk();
            $this->get(route('site.en.product', ['slug' => $product['en']]))->assertOk();
        }
    }
}
