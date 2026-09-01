<?php

namespace Tests\Feature;

use Tests\TestCase;

final class ExampleTest extends TestCase
{
    public function test_localized_homepages_render_without_a_redirect_loop(): void
    {
        foreach (['tr', 'en', 'de'] as $locale) {
            $response = $this->get(route('site.'.$locale.'.home'));

            $response
                ->assertOk()
                ->assertSee('lang="'.config('nuttime.locales.'.$locale.'.html').'"', false);
        }
    }
}
