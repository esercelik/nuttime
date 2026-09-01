<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [url('/'), url('/urunlerimiz'), url('/hakkimizda'), url('/sertifikalarimiz'), url('/iletisim')];

        foreach (['en', 'de'] as $locale) {
            $urls = [...$urls, url("/{$locale}"), url("/{$locale}/products"), url("/{$locale}/about-us"), url("/{$locale}/certificates"), url("/{$locale}/contact")];
        }
        if (Schema::hasTable('products')) {
            foreach (Product::query()->active()->orderBy('id')->get(['slug']) as $product) {
                $urls[] = url('/urunler/'.$product->slug);
                $urls[] = url('/en/products/'.$product->slug);
                $urls[] = url('/de/products/'.$product->slug);
            }
        }
        if (Schema::hasTable('categories')) {
            foreach (Category::query()->where('is_active', true)->orderBy('id')->get(['slug']) as $category) {
                $urls[] = url('/kategori/'.$category->slug);
                $urls[] = url('/en/category/'.$category->slug);
                $urls[] = url('/de/category/'.$category->slug);
            }
        }

        return response(view('sitemap', compact('urls'))->render(), 200, ['Content-Type' => 'application/xml']);
    }
}
