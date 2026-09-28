<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Models\Product;
use App\Support\LocalizedContent;
use Illuminate\Http\JsonResponse;

class V2StorefrontController extends Controller
{
    public function __invoke(string $locale, LocalizedContent $content): JsonResponse
    {
        abort_unless(array_key_exists($locale, config('nuttime.locales')), 404);
        app()->setLocale($locale);
        $copy = PageSection::query()->published()->where('page_key', 'v2')->with('translations')->get()
            ->mapWithKeys(fn (PageSection $section): array => [$section->key => $section->translationFor($locale)?->description])->filter();
        $products = Product::query()->active()->with(['translations', 'category.translations'])->orderBy('sort_order')->get()
            ->map(function (Product $product) use ($content, $locale): array {
                $data = $content->product($product, $locale);

                return array_intersect_key($data, array_flip(['source_slug', 'name', 'description', 'ingredients', 'allergen_information', 'weight_grams', 'primary_ingredient_percentage']));
            });

        return response()->json(['copy' => $copy, 'products' => $products])->header('Cache-Control', 'no-store');
    }
}
