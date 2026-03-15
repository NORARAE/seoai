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
        Schema::table('page_generation_batches', function (Blueprint $table) {
            // Track payload generation separately from publishing
            $table->unsignedInteger('payload_count')->default(0)->after('requested_count');
            $table->unsignedInteger('published_count')->default(0)->after('completed_count');
            $table->unsignedInteger('exported_count')->default(0)->after('published_count');
            
            // Publishing mode for this batch
            $table->boolean('auto_publish')->default(false)->after('batch_type');
            
            // Export tracking
            $table->string('export_path')->nullable()->after('error_summary');
            $table->string('export_format')->nullable()->after('export_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_generation_batches', function (Blueprint $table) {
            $table->dropColumn([
                'payload_count',
                'published_count',
                'exported_count',
                'auto_publish',
                'export_path',
                'export_format',
            ]);
        });
    }
};
