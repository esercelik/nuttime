<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;

final class LocalizedContent
{
    /**
     * @return array<string, mixed>
     */
    public function product(Product $product, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $translation = $product->translationFor($locale);
        $category = $product->category?->translationFor($locale);

        return [
            'id' => $product->id,
            'slug' => $translation?->slug ?: $product->slug,
            'slugs' => $this->productSlugs($product),
            'name' => $translation?->name ?: $product->name,
            'category' => $category?->name ?: $product->category?->name ?: __('site.catalog.default_category'),
            'category_slug' => $category?->slug ?: $product->category?->slug,
            'description' => $translation?->short_description ?: $translation?->description ?: $product->short_description ?: $product->description ?: '',
            'seo_title' => $translation?->meta_title ?: $product->seo_title,
            'seo_description' => $translation?->meta_description ?: $product->seo_description,
            'seo_canonical' => $product->seo_canonical,
            'previous_slugs' => $product->previous_slugs ?? [],
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'stock_tracking' => $product->stock_tracking,
            'weight_grams' => $product->weight_grams,
            'primary_ingredient_percentage' => $product->primary_ingredient_percentage,
            'feature_tags' => $product->feature_tags ?? [],
            'ingredients' => $translation?->ingredients,
            'allergen_information' => $translation?->allergen_information,
            'nutrition_facts' => $product->nutrition_facts ?? [],
            'packaging_details' => $product->packaging_details ?? [],
            'featured' => $product->is_featured,
            'accent' => '#d7b66c',
            'image' => $product->main_image ? asset('storage/'.$product->main_image) : null,
            'image_alt' => $product->main_image_alt ?: trim(($translation?->name ?: $product->name).' - '.($category?->name ?: $product->category?->name ?: 'Nuttime')),
            'gallery' => collect($product->additional_images ?? [])->filter()->map(fn (string $image): string => asset('storage/'.$image))->values()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function category(Category $category, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $translation = $category->translationFor($locale);

        return [
            'id' => $category->id,
            'slug' => $translation?->slug ?: $category->slug,
            'slugs' => $this->categorySlugs($category),
            'name' => $translation?->name ?: $category->name,
            'description' => $translation?->description ?: $category->description ?: '',
            'seo_title' => $translation?->meta_title ?: $category->seo_title,
            'seo_description' => $translation?->meta_description ?: $category->seo_description,
            'seo_canonical' => $category->seo_canonical,
            'image' => $category->image ? asset('storage/'.$category->image) : null,
            'image_alt' => $category->image_alt ?: trim(($translation?->name ?: $category->name).' '.__('site.catalog.category')),
        ];
    }

    /** @return array<string, string> */
    private function productSlugs(Product $product): array
    {
        return collect(array_keys(config('nuttime.locales')))->mapWithKeys(function (string $locale) use ($product): array {
            return [$locale => $product->translationFor($locale)?->slug ?: $product->slug];
        })->all();
    }

    /** @return array<string, string> */
    private function categorySlugs(Category $category): array
    {
        return collect(array_keys(config('nuttime.locales')))->mapWithKeys(function (string $locale) use ($category): array {
            return [$locale => $category->translationFor($locale)?->slug ?: $category->slug];
        })->all();
    }
}
