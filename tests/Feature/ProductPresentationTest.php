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
            ->assertSee('images/nuttime/peanut-butter.jpg', false);
    }
}
