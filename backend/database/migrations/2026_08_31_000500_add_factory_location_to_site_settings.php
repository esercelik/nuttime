<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('factory_name')->nullable();
            $table->text('factory_address')->nullable();
            $table->decimal('factory_map_latitude', 10, 7)->nullable();
            $table->decimal('factory_map_longitude', 10, 7)->nullable();
            $table->string('factory_google_maps_url')->nullable();
            $table->boolean('factory_map_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', fn (Blueprint $table) => $table->dropColumn(['factory_name', 'factory_address', 'factory_map_latitude', 'factory_map_longitude', 'factory_google_maps_url', 'factory_map_enabled']));
    }
};
