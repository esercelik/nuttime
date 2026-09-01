<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('seo_canonical')->nullable()->after('seo_description');
            $table->string('main_image_alt')->nullable()->after('main_image');
            $table->json('previous_slugs')->nullable()->after('slug');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('seo_canonical')->nullable()->after('seo_description');
            $table->string('image_alt')->nullable()->after('image');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('site_name');
            $table->text('working_hours')->nullable()->after('address');
            $table->string('default_og_image')->nullable()->after('logo');
            $table->string('twitter_handle')->nullable()->after('youtube');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['seo_canonical', 'main_image_alt', 'previous_slugs']));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['seo_title', 'seo_description', 'seo_canonical', 'image_alt']));
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn(['legal_name', 'working_hours', 'default_og_image', 'twitter_handle']));
    }
};
