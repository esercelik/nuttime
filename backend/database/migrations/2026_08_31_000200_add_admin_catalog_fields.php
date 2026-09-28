<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('main_image')->nullable();
            $table->json('additional_images')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('compare_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->boolean('stock_tracking')->default(false);
        });
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Nuttime');
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->text('footer_description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['name', 'slug', 'short_description', 'description', 'main_image', 'additional_images', 'seo_title', 'seo_description', 'price', 'compare_price', 'stock', 'stock_tracking']));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['name', 'slug', 'description', 'image']));
    }
};
