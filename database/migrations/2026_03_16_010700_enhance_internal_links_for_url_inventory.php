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
        Schema::table('internal_links', function (Blueprint $table) {
            $table->foreignId('source_url_id')->nullable()->after('source_page_id')->constrained('url_inventory')->nullOnDelete();
            $table->foreignId('target_url_id')->nullable()->after('source_url_id')->constrained('url_inventory')->nullOnDelete();

            $table->index(['site_id', 'source_url_id']);
            $table->index(['site_id', 'target_url_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_links', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_url_id');
            $table->dropConstrainedForeignId('target_url_id');
        });
    }
};
