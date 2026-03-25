<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_opportunities') || Schema::hasColumn('seo_opportunities', 'payload_id')) {
            return;
        }

        Schema::table('seo_opportunities', function (Blueprint $table): void {
            $table->foreignId('payload_id')->nullable()->after('location_page_id')
                ->constrained('page_payloads')->nullOnDelete();

            $table->index('payload_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('seo_opportunities') || ! Schema::hasColumn('seo_opportunities', 'payload_id')) {
            return;
        }

        Schema::table('seo_opportunities', function (Blueprint $table): void {
            $table->dropForeign(['payload_id']);
            $table->dropIndex(['payload_id']);
            $table->dropColumn('payload_id');
        });
    }
};
