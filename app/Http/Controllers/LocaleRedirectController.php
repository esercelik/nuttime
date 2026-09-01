<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

final class LocaleRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->to(route('site.'.$this->resolveLocale($request).'.home'));
    }

    private function resolveLocale(Request $request): string
    {
        $locales = array_keys(config('nuttime.locales'));
        $preference = $request->cookie(config('nuttime.preference_cookie'));

        if (in_array($preference, $locales, true)) {
            return $preference;
        }

        foreach ($request->getLanguages() as $language) {
            $locale = strtolower(substr($language, 0, 2));

            if (in_array($locale, $locales, true)) {
                return $locale;
            }
        }

        foreach (config('nuttime.country_headers') as $header) {
            $country = strtoupper((string) $request->header($header));

            if ($country === 'TR') {
                return 'tr';
            }

            if (in_array($country, ['DE', 'AT', 'CH', 'LI', 'LU'], true)) {
                return 'de';
            }

            if (filled($country)) {
                return 'en';
            }
        }

        return config('nuttime.default_locale');
    }
}
