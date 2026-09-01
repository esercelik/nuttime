<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Category;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $lastModified = Schema::hasTable('site_settings') ? SiteSetting::current()->updated_at : now();
        $urls = [
            $this->url(url('/'), $lastModified, 'weekly', '1.0'),
            $this->url(url('/urunlerimiz'), $lastModified, 'weekly', '0.9'),
            $this->url(url('/hakkimizda'), $lastModified, 'monthly', '0.7'),
            $this->url(url('/sertifikalarimiz'), $lastModified, 'monthly', '0.7'),
            $this->url(url('/iletisim'), $lastModified, 'monthly', '0.7'),
        ];

        foreach (['en', 'de'] as $locale) {
            $urls = [...$urls, $this->url(url("/{$locale}"), $lastModified, 'weekly', '0.7'), $this->url(url("/{$locale}/products"), $lastModified, 'weekly', '0.6'), $this->url(url("/{$locale}/about-us"), $lastModified, 'monthly', '0.5'), $this->url(url("/{$locale}/certificates"), $lastModified, 'monthly', '0.5'), $this->url(url("/{$locale}/contact"), $lastModified, 'monthly', '0.5')];
        }
        if (Schema::hasTable('products')) {
            foreach (Product::query()->active()->orderBy('id')->get(['slug', 'updated_at']) as $product) {
                $urls[] = $this->url(url('/urunler/'.$product->slug), $product->updated_at, 'weekly', '0.8');
                $urls[] = $this->url(url('/en/products/'.$product->slug), $product->updated_at, 'weekly', '0.6');
                $urls[] = $this->url(url('/de/products/'.$product->slug), $product->updated_at, 'weekly', '0.6');
            }
        }
        if (Schema::hasTable('categories')) {
            foreach (Category::query()->where('is_active', true)->orderBy('id')->get(['slug', 'updated_at']) as $category) {
                $urls[] = $this->url(url('/kategori/'.$category->slug), $category->updated_at, 'weekly', '0.7');
                $urls[] = $this->url(url('/en/category/'.$category->slug), $category->updated_at, 'weekly', '0.5');
                $urls[] = $this->url(url('/de/category/'.$category->slug), $category->updated_at, 'weekly', '0.5');
            }
        }
        if (Schema::hasTable('contents')) {
            foreach (Content::query()->published()->get(['slug', 'updated_at']) as $content) {
                $urls[] = $this->url(route('content', $content), $content->updated_at, 'monthly', '0.6');
            }
        }

        return response(view('sitemap', compact('urls'))->render(), 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * @return array{loc: string, lastmod: string, changefreq: string, priority: string}
     */
    private function url(string $location, $lastModified, string $changeFrequency, string $priority): array
    {
        return ['loc' => $location, 'lastmod' => $lastModified->toAtomString(), 'changefreq' => $changeFrequency, 'priority' => $priority];
    }
}
