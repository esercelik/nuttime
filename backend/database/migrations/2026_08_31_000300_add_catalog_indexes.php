<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order']);
            $table->index(['is_active', 'is_featured']);
        });
        Schema::table('categories', fn (Blueprint $table) => $table->index(['is_active', 'sort_order']));
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_is_active_sort_order_index');
            $table->dropIndex('products_is_active_is_featured_index');
        });
        Schema::table('categories', fn (Blueprint $table) => $table->dropIndex('categories_is_active_sort_order_index'));
    }
};
