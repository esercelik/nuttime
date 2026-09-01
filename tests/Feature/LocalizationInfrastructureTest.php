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

    public function test_platform_country_header_is_used_after_an_unsupported_browser_language(): void
    {
        $response = $this
            ->withHeaders([
                'Accept-Language' => 'es-ES,es;q=0.9',
                'X-Country-Code' => 'AT',
            ])
            ->get('/');

        $response->assertRedirect(route('site.de.home'));
    }

    public function test_unsupported_browser_language_without_country_falls_back_to_turkish(): void
    {
        $response = $this
            ->withHeaders(['Accept-Language' => 'es-ES,es;q=0.9'])
            ->get('/');

        $response->assertRedirect(route('site.tr.home'));
    }

    public function test_manual_language_selection_sets_the_preference_cookie_and_keeps_the_target_page(): void
    {
        $response = $this->post(route('locale.preference'), [
            'locale' => 'de',
            'redirect_to' => route('site.de.products'),
        ]);

        $response
            ->assertRedirect(route('site.de.products'))
            ->assertCookie(config('nuttime.preference_cookie'), 'de')
            ->assertCookieNotExpired(config('nuttime.preference_cookie'));
    }
}
