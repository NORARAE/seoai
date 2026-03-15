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
        // Only add if seo_opportunities table exists
        if (Schema::hasTable('seo_opportunities')) {
            Schema::table('seo_opportunities', function (Blueprint $table) {
                // Link to generated payload instead of location_page
                $table->foreignId('payload_id')->nullable()->after('location_page_id')
                    ->constrained('page_payloads')->nullOnDelete();
                
                $table->index('payload_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('seo_opportunities')) {
            Schema::table('seo_opportunities', function (Blueprint $table) {
                $table->dropForeign(['payload_id']);
                $table->dropColumn('payload_id');
            });
        }
    }
};
