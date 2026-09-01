<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

final class LocalePreferenceController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', array_keys(config('nuttime.locales')))],
            'redirect_to' => ['required', 'string', 'max:2048'],
        ]);
        $path = parse_url($validated['redirect_to'], PHP_URL_PATH);

        if (! is_string($path) || ! str_starts_with($path, '/')) {
            $path = route('site.'.$validated['locale'].'.home', [], false);
        }

        return redirect()->to($path)->cookie(cookie(
            name: config('nuttime.preference_cookie'),
            value: $validated['locale'],
            minutes: 60 * 24 * 365,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        ));
    }
}
