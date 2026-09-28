<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $routeLocale = null): Response
    {
        $locale = $routeLocale ?: $request->route('locale') ?: config('nuttime.default_locale');

        if (! array_key_exists($locale, config('nuttime.locales'))) {
            abort(404);
        }

        app()->setLocale($locale);
        app()->setFallbackLocale(config('nuttime.fallback_locale'));

        return $next($request);
    }
}
