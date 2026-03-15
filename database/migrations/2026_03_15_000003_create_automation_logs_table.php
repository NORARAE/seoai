<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tracks all automated job executions across the platform
     */
    public function up(): void
    {
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            
            // Job identification
            $table->string('job_name'); // daily_gsc_sync, opportunity_scan, etc.
            $table->string('job_class')->nullable(); // Full class name
            
            // Status: started, completed, failed, partial
            $table->string('status')->default('started');
            
            // Execution data
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            
            // Results
            $table->unsignedInteger('items_processed')->default(0);
            $table->unsignedInteger('items_succeeded')->default(0);
            $table->unsignedInteger('items_failed')->default(0);
            
            // Error tracking
            $table->text('error_message')->nullable();
            $table->json('error_context')->nullable();
            
            // Detailed results
            $table->json('summary')->nullable(); // Job-specific summary data
            $table->json('metadata')->nullable(); // Additional context
            
            $table->timestamps();
            
            // Indexes
            $table->index(['job_name', 'status', 'started_at']);
            $table->index(['site_id', 'job_name']);
            $table->index(['client_id', 'started_at']);
            $table->index('started_at'); // For cleanup queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
