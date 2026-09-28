<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class SeoMetadata
{
    /**
     * @param  array<string, mixed>  $settings
     * @param  array<int, array<string, mixed>>  $schemas
     * @return array<string, mixed>
     */
    public function page(string $title, string $description, string $canonical, array $settings, array $schemas = [], string $type = 'website', ?string $image = null, array $alternates = []): array
    {
        $siteName = $this->clean(Arr::get($settings, 'site_name'), config('app.name'));
        $resolvedTitle = $this->clean($title, $siteName);

        if ($resolvedTitle !== $siteName && ! Str::contains($resolvedTitle, $siteName)) {
            $resolvedTitle .= ' | '.$siteName;
        }

        return [
            'title' => Str::limit($resolvedTitle, 60, ''),
            'description' => Str::limit($this->clean($description, $this->clean(Arr::get($settings, 'seo_description'), $siteName)), 160, ''),
            'canonical' => $canonical,
            'image' => $image ?: $this->defaultImage($settings),
            'type' => $type,
            'locale' => config('nuttime.locales.'.app()->getLocale().'.og', config('seo.locale')),
            'robots' => config('seo.indexable') ? 'index,follow,max-image-preview:large' : 'noindex,nofollow',
            'twitter_handle' => $this->clean(Arr::get($settings, 'twitter_handle')),
            'schemas' => $schemas,
            'alternates' => $alternates,
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function organization(array $settings): array
    {
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => $this->clean(Arr::get($settings, 'legal_name'), $this->clean(Arr::get($settings, 'site_name'), config('app.name'))),
            'url' => url('/'),
        ];

        if ($logo = $this->storedImage(Arr::get($settings, 'logo'))) {
            $organization['logo'] = $logo;
        }

        foreach (['email', 'phone' => 'telephone'] as $source => $target) {
            $value = $this->clean(Arr::get($settings, $source));

            if ($value) {
                $organization[$target] = $value;
            }
        }

        $socialLinks = collect(['instagram', 'facebook', 'youtube'])
            ->map(fn (string $field): ?string => filter_var(Arr::get($settings, $field), FILTER_VALIDATE_URL) ?: null)
            ->filter()
            ->values()
            ->all();

        if ($socialLinks !== []) {
            $organization['sameAs'] = $socialLinks;
        }

        return $organization;
    }

    /**
     * @return array<string, mixed>
     */
    public function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'inLanguage' => config('nuttime.locales.'.app()->getLocale().'.html', 'tr-TR'),
        ];
    }

    /**
     * @param  array<int, array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public function breadcrumbs(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function product(array $product, array $settings, string $url): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->clean(Arr::get($product, 'name'), config('app.name')),
            'description' => $this->clean(Arr::get($product, 'description'), ''),
            'url' => $url,
            'brand' => ['@type' => 'Brand', 'name' => $this->clean(Arr::get($settings, 'site_name'), config('app.name'))],
        ];

        if ($image = $this->clean(Arr::get($product, 'image'))) {
            $schema['image'] = [[
                '@type' => 'ImageObject',
                'url' => $image,
                'caption' => $this->clean(Arr::get($product, 'image_alt'), $schema['name']),
            ]];
        }

        foreach (['sku', 'category'] as $field) {
            if ($value = $this->clean(Arr::get($product, $field))) {
                $schema[$field] = $value;
            }
        }

        if (is_numeric(Arr::get($product, 'price'))) {
            $offer = ['@type' => 'Offer', 'url' => $url, 'price' => (string) Arr::get($product, 'price'), 'priceCurrency' => 'TRY'];

            if (Arr::get($product, 'stock_tracking') && is_numeric(Arr::get($product, 'stock'))) {
                $offer['availability'] = Arr::get($product, 'stock') > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock';
            }

            $schema['offers'] = $offer;
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>|null
     */
    public function localBusiness(array $settings): ?array
    {
        $address = $this->clean(Arr::get($settings, 'factory_address'), $this->clean(Arr::get($settings, 'address')));

        if (! $address) {
            return null;
        }

        $business = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => url('/').'#local-business',
            'name' => $this->clean(Arr::get($settings, 'factory_name'), $this->clean(Arr::get($settings, 'site_name'), config('app.name'))),
            'url' => url('/'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => $address, 'addressCountry' => config('seo.country')],
        ];

        foreach (['phone' => 'telephone', 'email' => 'email', 'working_hours' => 'openingHours'] as $source => $target) {
            if ($value = $this->clean(Arr::get($settings, $source))) {
                $business[$target] = $value;
            }
        }

        return $business;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function defaultImage(array $settings): string
    {
        return $this->storedImage(Arr::get($settings, 'default_og_image'))
            ?? $this->storedImage(Arr::get($settings, 'logo'))
            ?? asset('images/nuttime/collection-banner.jpg');
    }

    private function storedImage(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('storage/'.$path);
    }

    private function clean(?string $value, string $fallback = ''): string
    {
        return Str::squish(strip_tags(filled($value) ? (string) $value : $fallback));
    }
}
