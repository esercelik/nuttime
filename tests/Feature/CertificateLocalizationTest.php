<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Menu;
use App\Models\PageSection;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class CertificateLocalizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_certificates_use_the_localized_content_and_image(): void
    {
        $certificate = Certificate::query()->create([
            'name' => 'Default certificate',
            'description' => 'Default description',
            'image' => 'media/certificates/default.jpg',
            'is_active' => true,
        ]);
        $certificate->translations()->createMany([
            ['locale' => 'fr', 'name' => 'Türkçe sertifika', 'description' => 'Türkçe açıklama', 'image' => 'media/certificates/tr.jpg'],
            ['locale' => 'en', 'name' => 'English certificate', 'description' => 'English description', 'image' => 'media/certificates/en.jpg'],
            ['locale' => 'de', 'name' => 'Deutsches Zertifikat', 'description' => 'Deutsche Beschreibung', 'image' => 'media/certificates/de.jpg'],
        ]);

        Certificate::query()->create([
            'name' => 'Archived certificate',
            'expires_at' => today()->subDay(),
            'is_active' => true,
        ]);

        $product = Product::factory()->create();

        $this->get(route('site.de.product', ['slug' => $product->slug]))
            ->assertOk()
            ->assertSee('Deutsches Zertifikat')
            ->assertSee('Deutsche Beschreibung')
            ->assertSee('/storage/media/certificates/de.jpg', false)
            ->assertDontSee('Archived certificate')
            ->assertDontSee('Default certificate');

        $this->get(route('site.de.home'))->assertOk()->assertDontSee('Deutsches Zertifikat')->assertSee('href="'.route('site.de.certificates').'/"', false);
        $this->get(route('site.de.products'))->assertOk()->assertDontSee('Deutsches Zertifikat');
        $this->get(route('site.de.certificates'))->assertOk()->assertSee('Deutsches Zertifikat')->assertSee('Archived certificate')->assertSee('ARCHIVIERT / ABGELAUFEN');
        $this->get(route('sitemap'))->assertOk()->assertSee(route('site.de.certificates'));
    }

    public function test_managed_home_sections_remain_hidden_while_nested_certificate_links_are_available(): void
    {
        $section = PageSection::query()->create(['page_key' => 'home', 'key' => 'certificates', 'type' => 'certificates', 'status' => 'published', 'is_active' => true]);
        $section->translations()->create(['locale' => 'fr', 'title' => 'Managed certificate section']);
        foreach (['header-primary', 'footer-primary'] as $key) {
            $menu = Menu::query()->create(['key' => $key, 'name' => $key, 'is_active' => true]);
            $parent = $menu->items()->create(['link_type' => 'internal', 'route_name' => 'products', 'is_active' => true]);
            $parent->translations()->create(['locale' => 'fr', 'label' => 'Product navigation']);
            $item = $menu->items()->create(['parent_id' => $parent->id, 'link_type' => 'internal', 'route_name' => 'certificates', 'is_active' => true]);
            $item->translations()->create(['locale' => 'fr', 'label' => 'Managed certificate link']);
        }

        $this->get(route('site.fr.home'))->assertOk()
            ->assertSee('Product navigation')
            ->assertDontSee('Managed certificate section')
            ->assertSee('Managed certificate link');
    }
}
