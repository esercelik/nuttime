<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class ProductDataFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_product_records_are_the_same_records_rendered_by_public_catalog_routes(): void
    {
        $product = Product::factory()->create([
            'name' => 'Admin veri akışı ürünü',
            'slug' => 'admin-veri-akisi-urunu',
            'short_description' => 'İlk admin açıklaması.',
            'is_active' => true,
            'sort_order' => 10,
        ]);
        $product->translations()->create([
            'locale' => 'fr',
            'name' => 'Admin veri akışı ürünü',
            'slug' => 'admin-veri-akisi-urunu',
            'short_description' => 'İlk admin açıklaması.',
        ]);

        $this->assertSame(Product::class, ProductResource::getModel());

        $this->get(route('site.fr.home'))->assertOk()->assertSee('İlk admin açıklaması.');
        $this->get(route('site.fr.products'))->assertOk()->assertSee('İlk admin açıklaması.');
        $this->get(route('site.fr.product', ['slug' => 'admin-veri-akisi-urunu']))->assertOk()->assertSee('İlk admin açıklaması.');

        $product->update(['short_description' => 'Güncellenmiş admin açıklaması.']);
        $product->translations()->where('locale', 'fr')->update(['short_description' => 'Güncellenmiş admin açıklaması.']);

        $this->get(route('site.fr.products'))
            ->assertOk()
            ->assertSee('Güncellenmiş admin açıklaması.')
            ->assertDontSee('İlk admin açıklaması.');

        $product->update(['is_active' => false]);

        $this->get(route('site.fr.products'))
            ->assertOk()
            ->assertDontSee('Admin veri akışı ürünü');
    }
}
