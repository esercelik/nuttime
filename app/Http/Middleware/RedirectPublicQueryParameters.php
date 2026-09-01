<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPublicQueryParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->query() !== [] && ! $request->is('admin', 'admin/*', 'api/*', 'livewire/*')) {
            return redirect()->to($request->url(), 301);
        }

        return $next($request);
    }
}
