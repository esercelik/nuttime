<?php

namespace Tests\Feature;

use Tests\TestCase;

final class LocalizedNavigationTest extends TestCase
{
    public function test_english_homepage_renders_localized_navigation_and_links(): void
    {
        $response = $this->get('/en/');

        $response->assertSee('Home')
            ->assertSee('Products')
            ->assertSee('About us')
            ->assertSee('/en/products"', false)
            ->assertSee('/en/about-us"', false)
            ->assertSee('/en/contact"', false);
    }
}
