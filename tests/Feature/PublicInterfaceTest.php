<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\Menu;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class PublicInterfaceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_page_types_render_in_every_supported_language(): void
    {
        foreach (array_keys(config('nuttime.locales')) as $locale) {
            foreach (['home', 'products', 'about', 'contact', 'contents'] as $page) {
                $this->get(route('site.'.$locale.'.'.$page))->assertOk()
                    ->assertSee('id="main-content"', false)
                    ->assertDontSee('href="tel:"', false)
                    ->assertDontSee('+90 212 123 45 67');
            }
        }
    }

    public function test_mobile_navigation_keeps_contact_with_a_managed_menu(): void
    {
        $menu = Menu::query()->create(['key' => 'header-primary', 'name' => 'Navigation', 'is_active' => true]);
        $item = $menu->items()->create(['link_type' => 'internal', 'route_name' => 'products', 'is_active' => true]);
        $item->translations()->create(['locale' => 'tr', 'label' => 'Ürünler']);

        $response = $this->get(route('site.tr.home'))->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $links = (new \DOMXPath($document))->query('//*[@id="mobile-navigation"]//a[@href="'.route('site.tr.contact').'"]');
        $this->assertSame(1, $links->length);
    }

    public function test_configured_phone_is_shown_with_a_dialable_link(): void
    {
        SiteSetting::current()->update(['phone' => '+90 (535) 100 60 30']);
        $this->get(route('site.tr.contact'))->assertOk()
            ->assertSee('href="tel:+905351006030"', false)
            ->assertSee('+90 (535) 100 60 30');
    }

    public function test_contact_validation_errors_remain_attached_to_the_fields(): void
    {
        $response = $this->from(route('site.tr.contact'))->post(route('site.tr.contact.store'), [
            'name' => '', 'email' => 'invalid', 'message' => '',
        ]);
        $response->assertSessionHasErrors(['name', 'email', 'message']);

        $this->withCookie(config('session.cookie'), session()->getId())->get(route('site.tr.contact'))->assertOk()
            ->assertSee('aria-describedby="contact-name-error"', false)
            ->assertSee('aria-describedby="contact-email-error"', false)
            ->assertSee('aria-describedby="contact-message-error"', false);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_valid_contact_submission_displays_confirmation_and_stores_the_message(): void
    {
        $this->from(route('site.tr.contact'))->post(route('site.tr.contact.store'), [
            'name' => 'Test visitor', 'email' => 'visitor@example.test', 'message' => 'Product information request.',
        ])->assertRedirect(route('site.tr.contact'))->assertSessionHas('success');
        $this->assertDatabaseHas(ContactMessage::class, ['email' => 'visitor@example.test', 'locale' => 'tr']);
        $this->get(route('site.tr.contact'))->assertSee('role="status"', false);
    }

    public function test_content_without_a_cover_has_no_empty_image_box(): void
    {
        Content::query()->create(['title' => 'Test story', 'slug' => 'test-story', 'body' => 'Story body', 'status' => 'published', 'published_at' => now()]);
        $this->get(route('site.tr.contents'))->assertOk()->assertSee('Test story')->assertDontSee('class="catalog-card__media"', false);
        $this->get(route('site.tr.content', ['slug' => 'test-story']))->assertOk()->assertSee('Story body');
    }
}
