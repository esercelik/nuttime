<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class ProductHeroSliderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_slider_renders_active_products_and_omits_inactive_products(): void
    {
        $activeProduct = Product::factory()->create([
            'name' => 'Dinamik Antep Ezmesi',
            'slug' => 'dinamik-antep-ezmesi',
            'short_description' => 'Yönetim panelinden eklenen aktif ürün.',
            'main_image' => 'products/dinamik-antep.jpg',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);
        Product::factory()->create([
            'name' => 'Pasif Ürün',
            'slug' => 'pasif-urun',
            'main_image' => 'products/pasif-urun.jpg',
            'is_active' => false,
        ]);

        $response = $this->get(route('site.tr.home'));

        $response->assertSee($activeProduct->name)
            ->assertSee($activeProduct->short_description)
            ->assertSee(route('site.tr.product', ['slug' => $activeProduct->slug]), false)
            ->assertDontSee('Pasif Ürün');
    }
}
