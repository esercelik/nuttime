<?php

namespace Tests\Feature;

use App\Models\PageSection;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class V2StorefrontTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_published_v2_content_and_active_products_are_read_from_admin_records(): void
    {
        $section = PageSection::query()->create(['page_key' => 'v2', 'key' => 'heroHeading', 'type' => 'custom', 'status' => 'published', 'is_active' => true]);
        $section->translations()->create(['locale' => 'tr', 'title' => 'Hero', 'description' => 'Yönetilen başlık']);
        $product = Product::factory()->create(['name' => 'Yönetilen ürün', 'is_active' => true]);
        Product::factory()->create(['name' => 'Gizli ürün', 'is_active' => false]);
        $this->getJson('/api/cms/v2/tr')->assertOk()->assertJsonPath('copy.heroHeading', 'Yönetilen başlık')->assertJsonPath('products.0.name', $product->name)->assertJsonCount(1, 'products')->assertJsonMissingPath('products.0.stock');
        $section->update(['status' => 'draft']);
        $this->getJson('/api/cms/v2/tr')->assertOk()->assertJsonMissingPath('copy.heroHeading');
        $this->getJson('/api/cms/v2/invalid')->assertNotFound();
    }

    public function test_admin_routes_require_login_and_reject_users_without_cms_roles(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_open_dashboard_products_and_v2_content(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        foreach (['/admin', '/admin/products', '/admin/page-sections', '/admin/site-settings'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_v2_import_is_repeatable_and_preserves_admin_edits(): void
    {
        $this->artisan('nuttime:import-v2-content')->assertSuccessful();
        $section = PageSection::query()->where('page_key', 'v2')->where('key', 'heroHeading')->firstOrFail();
        $section->translations()->where('locale', 'tr')->update(['description' => 'Admin değişikliği']);
        $count = PageSection::query()->count();
        $this->artisan('nuttime:import-v2-content')->assertSuccessful();
        $this->assertSame($count, PageSection::query()->count());
        $this->getJson('/api/cms/v2/tr')->assertJsonPath('copy.heroHeading', 'Admin değişikliği');
    }
}
