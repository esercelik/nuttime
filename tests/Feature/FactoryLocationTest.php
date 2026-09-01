<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class FactoryLocationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_contact_renders_the_interactive_factory_map_from_site_settings(): void
    {
        config()->set('services.google_maps.embed_api_key', 'test-embed-key');

        $this->createFactorySettings();

        $response = $this->get(route('site.tr.contact'));

        $response
            ->assertSee('factory-location--contact', false)
            ->assertSee('Nuttime Üretim Tesisi')
            ->assertSee('İstanbul, Türkiye')
            ->assertSee('info@nuttime.test')
            ->assertSee('+90 212 555 00 00')
            ->assertSee('https://www.google.com/maps/embed/v1/place?key=test-embed-key&amp;q=41.0151370%2C28.9795300', false)
            ->assertSee('loading="lazy"', false)
            ->assertSee('referrerpolicy="no-referrer-when-downgrade"', false)
            ->assertSee('https://www.google.com/maps/place/Nuttime', false)
            ->assertSee('Yol tarifi al');
    }

    public function test_contact_uses_a_google_maps_link_fallback_when_the_embed_api_key_is_missing(): void
    {
        config()->set('services.google_maps.embed_api_key', null);

        $this->createFactorySettings();

        $response = $this->get(route('site.tr.contact'));

        $response
            ->assertSee('map-frame--fallback', false)
            ->assertDontSee('maps/embed/v1/place', false)
            ->assertSee('Haritada gör')
            ->assertSee('https://www.google.com/maps/place/Nuttime', false);
    }

    public function test_contact_uses_the_link_fallback_when_factory_coordinates_are_missing(): void
    {
        config()->set('services.google_maps.embed_api_key', 'test-embed-key');

        $this->createFactorySettings([
            'factory_map_latitude' => null,
            'factory_map_longitude' => null,
        ]);

        $response = $this->get(route('site.tr.contact'));

        $response
            ->assertSee('map-frame--fallback', false)
            ->assertDontSee('maps/embed/v1/place', false)
            ->assertSee('Haritada gör');
    }

    public function test_contact_hides_map_links_when_google_maps_url_is_missing(): void
    {
        config()->set('services.google_maps.embed_api_key', 'test-embed-key');

        $this->createFactorySettings([
            'factory_google_maps_url' => null,
        ]);

        $response = $this->get(route('site.tr.contact'));

        $response
            ->assertSee('maps/embed/v1/place', false)
            ->assertDontSee('Yol tarifi al')
            ->assertDontSee('Haritada gör');
    }

    public function test_homepage_and_contact_share_the_same_factory_settings(): void
    {
        $this->createFactorySettings([
            'factory_name' => 'Güncellenen Nuttime Tesisi',
            'factory_address' => 'Pendik, İstanbul',
        ]);

        $this->get(route('site.tr.home'))
            ->assertSee('factory-location--homepage', false)
            ->assertSee('Güncellenen Nuttime Tesisi')
            ->assertSee('Pendik, İstanbul');

        $this->get(route('site.tr.contact'))
            ->assertSee('factory-location--contact', false)
            ->assertSee('Güncellenen Nuttime Tesisi')
            ->assertSee('Pendik, İstanbul');
    }

    #[DataProvider('unavailableFactoryLocations')]
    public function test_contact_hides_the_factory_location_when_it_is_disabled_or_has_no_address(array $overrides): void
    {
        $this->createFactorySettings($overrides);

        $this->get(route('site.tr.contact'))
            ->assertDontSee('factory-location--contact', false);
    }

    /** @return array<string, array{0: array<string, mixed>}> */
    public static function unavailableFactoryLocations(): array
    {
        return [
            'disabled in the admin panel' => [['factory_map_enabled' => false]],
            'missing factory address' => [['factory_address' => null]],
        ];
    }

    public function test_contact_form_continues_to_store_messages_when_factory_location_is_visible(): void
    {
        $this->createFactorySettings();

        $response = $this->from(route('site.tr.contact'))->post(route('site.tr.contact.store'), [
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse@example.test',
            'message' => 'Ürünleriniz hakkında bilgi almak istiyorum.',
            'website' => '',
        ]);

        $response->assertRedirect(route('site.tr.contact'));
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse@example.test',
            'message' => 'Ürünleriniz hakkında bilgi almak istiyorum.',
            'locale' => 'tr',
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function createFactorySettings(array $overrides = []): SiteSetting
    {
        return SiteSetting::query()->create([
            'site_name' => 'Nuttime',
            'email' => 'info@nuttime.test',
            'phone' => '+90 212 555 00 00',
            'factory_name' => 'Nuttime Üretim Tesisi',
            'factory_address' => 'İstanbul, Türkiye',
            'factory_map_latitude' => '41.0151370',
            'factory_map_longitude' => '28.9795300',
            'factory_google_maps_url' => 'https://www.google.com/maps/place/Nuttime',
            'factory_map_enabled' => true,
            ...$overrides,
        ]);
    }
}
