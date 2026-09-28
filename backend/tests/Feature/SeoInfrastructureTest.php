<?php

namespace Tests\Feature;

use Tests\TestCase;

final class SeoInfrastructureTest extends TestCase
{
    public function test_localized_page_emits_canonical_and_all_language_alternates(): void
    {
        $response = $this->get(route('site.de.products'));

        $response
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('site.de.products').'">', false)
            ->assertSee('<link rel="alternate" hreflang="tr" href="'.route('site.tr.products').'">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.route('site.en.products').'">', false)
            ->assertSee('<link rel="alternate" hreflang="de" href="'.route('site.de.products').'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.route('site.tr.products').'">', false)
            ->assertSee('<meta property="og:locale" content="de_DE">', false);
    }

    public function test_sitemap_includes_each_locale_homepage(): void
    {
        $response = $this->get(route('sitemap'));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('site.tr.home'), false)
            ->assertSee(route('site.en.home'), false)
            ->assertSee(route('site.de.home'), false);
    }
}
