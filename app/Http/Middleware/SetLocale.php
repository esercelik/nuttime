<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?: 'tr';
        if (! in_array($locale, config('nuttime.locales'), true)) {
            abort(404);
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
