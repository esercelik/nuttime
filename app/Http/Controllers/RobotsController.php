<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = ['User-agent: *'];

        if (config('seo.indexable')) {
            $lines = [...$lines, 'Allow: /', 'Disallow: /admin', 'Disallow: /login', 'Disallow: /api', 'Disallow: /panel', 'Sitemap: '.route('sitemap')];
        } else {
            $lines[] = 'Disallow: /';
        }

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
