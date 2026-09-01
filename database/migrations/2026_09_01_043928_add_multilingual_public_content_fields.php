<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->json('translations')->nullable()->after('seo_canonical');
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('translations')->nullable()->after('seo_description');
        });

        DB::table('products')->orderBy('id')->eachById(function (object $product): void {
            foreach (['tr', 'en', 'de'] as $locale) {
                DB::table('product_translations')->insertOrIgnore([
                    [
                        'product_id' => $product->id,
                        'locale' => $locale,
                        'name' => $product->name ?: 'Nuttime',
                        'slug' => $locale === 'tr' ? ($product->slug ?: 'urun-'.$product->id) : $locale.'-'.($product->slug ?: 'product-'.$product->id),
                        'short_description' => $product->short_description,
                        'description' => $product->description,
                        'meta_title' => $product->seo_title,
                        'meta_description' => $product->seo_description,
                    ],
                ]);
            }
        });

        DB::table('categories')->orderBy('id')->eachById(function (object $category): void {
            foreach (['tr', 'en', 'de'] as $locale) {
                DB::table('category_translations')->insertOrIgnore([
                    [
                        'category_id' => $category->id,
                        'locale' => $locale,
                        'name' => $category->name ?: 'Nuttime',
                        'slug' => $locale === 'tr' ? ($category->slug ?: 'kategori-'.$category->id) : $locale.'-'.($category->slug ?: 'category-'.$category->id),
                        'description' => $category->description,
                        'meta_title' => $category->seo_title,
                        'meta_description' => $category->seo_description,
                    ],
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', fn (Blueprint $table) => $table->dropColumn('translations'));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn('translations'));
    }
};
