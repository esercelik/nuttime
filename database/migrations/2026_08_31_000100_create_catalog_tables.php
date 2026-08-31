<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', fn (Blueprint $t) => [$t->id(), $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete(), $t->boolean('is_active')->default(true), $t->unsignedInteger('sort_order')->default(0), $t->timestamps(), $t->softDeletes()]);
        Schema::create('category_translations', fn (Blueprint $t) => [$t->id(), $t->foreignId('category_id')->constrained()->cascadeOnDelete(), $t->string('locale', 5), $t->string('name'), $t->string('slug'), $t->text('description')->nullable(), $t->string('meta_title')->nullable(), $t->text('meta_description')->nullable(), $t->unique(['category_id', 'locale']), $t->unique(['locale', 'slug'])]);
        Schema::create('products', fn (Blueprint $t) => [$t->id(), $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete(), $t->string('sku')->nullable()->unique(), $t->string('status')->default('draft'), $t->boolean('is_featured')->default(false), $t->unsignedInteger('sort_order')->default(0), $t->timestamp('published_at')->nullable(), $t->json('nutrition_facts')->nullable(), $t->json('packaging_details')->nullable(), $t->timestamps(), $t->softDeletes()]);
        Schema::create('product_translations', fn (Blueprint $t) => [$t->id(), $t->foreignId('product_id')->constrained()->cascadeOnDelete(), $t->string('locale', 5), $t->string('name'), $t->string('slug'), $t->text('short_description')->nullable(), $t->longText('description')->nullable(), $t->longText('ingredients')->nullable(), $t->longText('allergen_information')->nullable(), $t->string('meta_title')->nullable(), $t->text('meta_description')->nullable(), $t->unique(['product_id', 'locale']), $t->unique(['locale', 'slug'])]);
        Schema::create('product_images', fn (Blueprint $t) => [$t->id(), $t->foreignId('product_id')->constrained()->cascadeOnDelete(), $t->string('path'), $t->string('alt_text_tr')->nullable(), $t->string('alt_text_en')->nullable(), $t->string('alt_text_de')->nullable(), $t->boolean('is_cover')->default(false), $t->unsignedInteger('sort_order')->default(0), $t->timestamps()]);
        Schema::create('pages', fn (Blueprint $t) => [$t->id(), $t->string('key')->unique(), $t->boolean('is_active')->default(true), $t->timestamps()]);
        Schema::create('page_translations', fn (Blueprint $t) => [$t->id(), $t->foreignId('page_id')->constrained()->cascadeOnDelete(), $t->string('locale', 5), $t->string('title'), $t->string('slug'), $t->longText('content'), $t->string('meta_title')->nullable(), $t->text('meta_description')->nullable(), $t->unique(['page_id', 'locale']), $t->unique(['locale', 'slug'])]);
        Schema::create('contact_messages', fn (Blueprint $t) => [$t->id(), $t->string('name'), $t->string('email'), $t->string('phone')->nullable(), $t->string('subject')->nullable(), $t->text('message'), $t->string('locale', 5)->default('tr'), $t->boolean('is_read')->default(false), $t->timestamp('read_at')->nullable(), $t->timestamps()]);
    }

    public function down(): void
    {
        foreach (['contact_messages', 'page_translations', 'pages', 'product_images', 'product_translations', 'products', 'category_translations', 'categories'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
