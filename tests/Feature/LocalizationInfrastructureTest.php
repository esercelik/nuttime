<?php

namespace Tests\Feature;

use Tests\TestCase;

final class LocalizationInfrastructureTest extends TestCase
{
    public function test_saved_locale_preference_takes_priority_over_browser_language(): void
    {
        $response = $this
            ->withCookie(config('nuttime.preference_cookie'), 'en')
            ->withHeaders(['Accept-Language' => 'de-DE,de;q=0.9'])
            ->get('/');

        $response->assertRedirect(route('site.en.home'));
    }

    public function test_browser_language_is_used_when_no_preference_exists(): void
    {
        $response = $this
            ->withHeaders(['Accept-Language' => 'de-DE,de;q=0.9'])
            ->get('/');

        $response->assertRedirect(route('site.de.home'));
    }

    public function test_supported_browser_language_takes_priority_over_platform_country_header(): void
    {
        $response = $this
            ->withHeaders([
                'Accept-Language' => 'es-ES,es;q=0.9',
                'X-Country-Code' => 'AT',
            ])
            ->get('/');

        $response->assertRedirect(route('site.es.home'));
    }

    public function test_supported_browser_language_redirects_to_its_localized_homepage(): void
    {
        $response = $this
            ->withHeaders(['Accept-Language' => 'es-ES,es;q=0.9'])
            ->get('/');

        $response->assertRedirect(route('site.es.home'));
    }

    public function test_manual_language_selection_sets_the_preference_cookie_and_keeps_the_target_page(): void
    {
        $response = $this->post(route('locale.preference'), [
            'locale' => 'fr',
            'redirect_to' => route('site.fr.products'),
        ]);

        $response
            ->assertRedirect(route('site.fr.products'))
            ->assertCookie(config('nuttime.preference_cookie'), 'fr')
            ->assertCookieNotExpired(config('nuttime.preference_cookie'));
    }
}
