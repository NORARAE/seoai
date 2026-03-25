<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');

            // Who/how it was triggered
            $table->enum('triggered_by_type', ['manual', 'scheduled', 'api'])->default('scheduled');
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();

            // What kind of crawl
            $table->enum('crawl_mode', ['full', 'incremental', 'sitemap_only'])->default('full');
            $table->enum('seed_source', ['sitemap', 'homepage', 'manual'])->default('homepage');

            // Lifecycle
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Summary counts (written when run completes)
            $table->unsignedInteger('pages_discovered')->default(0);
            $table->unsignedInteger('pages_crawled')->default(0);
            $table->unsignedInteger('pages_failed')->default(0);
            $table->unsignedInteger('opportunities_found')->default(0);

            // Diagnostics
            $table->text('error_summary')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['site_id', 'status']);
            $table->index(['site_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_runs');
    }
};
