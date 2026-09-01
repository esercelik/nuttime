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
            'packaging_details' => ['Ambalaj' => 'Cam kavanoz'],
            'nutrition_facts' => ['Enerji' => '600 kcal'],
        ]);

        $this->get(route('site.tr.product', ['slug' => $product->slug]))
            ->assertSee('Fındık Ezmesi')
            ->assertSee('250 g')
            ->assertSee('Şeker ilavesiz · Vegan')
            ->assertSee('Cam kavanoz')
            ->assertSee('600 kcal');
    }
}
