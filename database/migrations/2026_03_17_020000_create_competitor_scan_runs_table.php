<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_scan_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('competitor_domain_id')->constrained('competitor_domains')->cascadeOnDelete();
            $table->enum('triggered_by_type', ['auto', 'manual', 'api'])->default('auto');
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('urls_discovered')->default(0);
            $table->unsignedInteger('urls_compared')->default(0);
            $table->unsignedInteger('gaps_found')->default(0);
            $table->boolean('credit_consumed')->default(false);
            $table->foreignId('usage_record_id')->nullable()->constrained('usage_records')->nullOnDelete();
            $table->text('error_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'status']);
            $table->index(['competitor_domain_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_scan_runs');
    }
};