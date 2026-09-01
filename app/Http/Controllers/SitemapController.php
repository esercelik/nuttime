<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Support\LocalizedUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

final class SitemapController extends Controller
{
    public function __construct(private LocalizedUrl $localizedUrl) {}

    public function __invoke(): Response
    {
        $lastModified = Schema::hasTable('site_settings') ? SiteSetting::current()->updated_at : now();
        $urls = [];

        foreach (array_keys(config('nuttime.locales')) as $locale) {
            foreach (['home' => ['weekly', '1.0'], 'products' => ['weekly', '0.9'], 'about' => ['monthly', '0.7'], 'certificates' => ['monthly', '0.7'], 'contact' => ['monthly', '0.7'], 'contents' => ['weekly', '0.6']] as $page => [$frequency, $priority]) {
                $urls[] = $this->url($this->localizedUrl->route($page, $locale), $lastModified, $frequency, $priority);
            }
        }

        if (Schema::hasTable('products')) {
            Product::query()->with('translations')->active()->orderBy('id')->get()->each(function (Product $product) use (&$urls): void {
                foreach (array_keys(config('nuttime.locales')) as $locale) {
                    $slug = $product->translationFor($locale)?->slug ?: $product->slug;
                    $urls[] = $this->url($this->localizedUrl->route('product', $locale, ['slug' => $slug]), $product->updated_at, 'weekly', '0.8');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Category::query()->with('translations')->where('is_active', true)->orderBy('id')->get()->each(function (Category $category) use (&$urls): void {
                foreach (array_keys(config('nuttime.locales')) as $locale) {
                    $slug = $category->translationFor($locale)?->slug ?: $category->slug;
                    $urls[] = $this->url($this->localizedUrl->route('category', $locale, ['slug' => $slug]), $category->updated_at, 'weekly', '0.7');
                }
            });
        }

        if (Schema::hasTable('contents')) {
            Content::query()->published()->get()->each(function (Content $content) use (&$urls): void {
                $translations = collect($content->translations ?? [])->keyBy('locale');

                foreach (array_keys(config('nuttime.locales')) as $locale) {
                    $slug = $translations->get($locale)['slug'] ?? $translations->get('en')['slug'] ?? $content->slug;
                    $urls[] = $this->url($this->localizedUrl->route('content', $locale, ['slug' => $slug]), $content->updated_at, 'monthly', '0.6');
                }
            });
        }

        return response(view('sitemap', compact('urls'))->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** @return array{loc: string, lastmod: string, changefreq: string, priority: string} */
    private function url(string $location, $lastModified, string $changeFrequency, string $priority): array
    {
        return ['loc' => $location, 'lastmod' => $lastModified->toAtomString(), 'changefreq' => $changeFrequency, 'priority' => $priority];
    }
}
