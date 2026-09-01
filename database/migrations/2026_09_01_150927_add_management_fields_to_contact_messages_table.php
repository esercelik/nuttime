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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->boolean('is_answered')->default(false)->after('is_read');
            $table->timestamp('answered_at')->nullable()->after('read_at');
            $table->timestamp('archived_at')->nullable()->after('answered_at');
            $table->text('internal_note')->nullable()->after('archived_at');
            $table->index(['is_read', 'archived_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex(['is_read', 'archived_at', 'created_at']);
            $table->dropColumn(['is_answered', 'answered_at', 'archived_at', 'internal_note']);
        });
    }
};
