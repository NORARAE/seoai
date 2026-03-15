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
        Schema::create('page_generation_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('initiated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Batch metadata
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('opportunity_source', ['manual', 'scan', 'scheduled'])->default('manual');
            
            // Progress tracking
            $table->integer('requested_count')->default(0);
            $table->integer('successful_count')->default(0);
            $table->integer('failed_count')->default(0);
            
            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            
            // Error tracking
            $table->text('error_summary')->nullable();
            $table->json('failed_items')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['site_id', 'status']);
            $table->index(['client_id', 'created_at']);
            $table->index('opportunity_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_generation_batches');
    }
};
