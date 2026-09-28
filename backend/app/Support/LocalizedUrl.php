<?php

namespace App\Support;

final class LocalizedUrl
{
    /**
     * @param  array<string, string>  $parameters
     */
    public function route(string $page, ?string $locale = null, array $parameters = []): string
    {
        $locale ??= app()->getLocale();

        return route('site.'.$locale.'.'.$page, $parameters);
    }

    /**
     * @param  array<string, string>  $slugs
     * @return array<string, string>
     */
    public function alternatives(string $page, array $slugs = []): array
    {
        return collect(array_keys(config('nuttime.locales')))
            ->mapWithKeys(fn (string $locale): array => [$locale => $this->route($page, $locale, isset($slugs[$locale]) ? ['slug' => $slugs[$locale]] : [])])
            ->all();
    }
}
