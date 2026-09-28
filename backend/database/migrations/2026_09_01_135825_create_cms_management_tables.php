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
        Schema::create('sliders', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('status')->default('published');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('slider_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('slider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('published');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('background_image')->nullable();
            $table->string('mobile_background_image')->nullable();
            $table->string('product_image')->nullable();
            $table->string('decoration_image')->nullable();
            $table->string('mobile_decoration_image')->nullable();
            $table->string('background_color', 16)->nullable();
            $table->string('text_color', 16)->nullable();
            $table->string('accent_color', 16)->nullable();
            $table->timestamp('published_from')->nullable();
            $table->timestamp('published_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['slider_id', 'is_active', 'sort_order']);
        });

        Schema::create('slider_item_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('slider_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->unique(['slider_item_id', 'locale']);
        });

        Schema::create('page_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('page_key')->default('home');
            $table->string('key');
            $table->string('type');
            $table->string('status')->default('published');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('background_color', 16)->nullable();
            $table->string('text_color', 16)->nullable();
            $table->string('accent_color', 16)->nullable();
            $table->string('variant')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('published_from')->nullable();
            $table->timestamp('published_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['page_key', 'key']);
            $table->index(['page_key', 'is_active', 'sort_order']);
        });

        Schema::create('page_section_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->unique(['page_section_id', 'locale']);
        });

        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('location')->default('header');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('link_type')->default('internal');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['menu_id', 'parent_id', 'is_active', 'sort_order']);
        });

        Schema::create('menu_item_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('label');
            $table->unique(['menu_item_id', 'locale']);
        });

        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('folder')->nullable();
            $table->string('title')->nullable();
            $table->json('alt_texts')->nullable();
            $table->string('checksum', 64)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('certificate_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['certificate_id', 'locale']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 40);
            $table->morphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['event', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('super_admin')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedInteger('weight_grams')->nullable()->after('packaging_details');
            $table->decimal('primary_ingredient_percentage', 5, 2)->nullable()->after('weight_grams');
            $table->json('feature_tags')->nullable()->after('primary_ingredient_percentage');
            $table->json('certificate_ids')->nullable()->after('feature_tags');
            $table->string('social_image')->nullable()->after('main_image');
        });

        Schema::table('certificates', function (Blueprint $table): void {
            $table->string('issuer')->nullable()->after('name');
            $table->string('certificate_number')->nullable()->after('issuer');
            $table->date('issued_at')->nullable()->after('certificate_number');
            $table->date('expires_at')->nullable()->after('issued_at');
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->json('design_tokens')->nullable()->after('translations');
            $table->json('seo_settings')->nullable()->after('design_tokens');
            $table->json('active_locales')->nullable()->after('seo_settings');
            $table->string('default_locale', 5)->nullable()->after('active_locales');
            $table->string('contact_recipient')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn(['design_tokens', 'seo_settings', 'active_locales', 'default_locale', 'contact_recipient']));
        Schema::table('certificates', fn (Blueprint $table) => $table->dropColumn(['issuer', 'certificate_number', 'issued_at', 'expires_at']));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['weight_grams', 'primary_ingredient_percentage', 'feature_tags', 'certificate_ids', 'social_image']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'last_login_at']));
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('certificate_translations');
        Schema::dropIfExists('media');
        Schema::dropIfExists('menu_item_translations');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('page_section_translations');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('slider_item_translations');
        Schema::dropIfExists('slider_items');
        Schema::dropIfExists('sliders');
    }
};
