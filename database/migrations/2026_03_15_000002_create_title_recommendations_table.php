<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Stores AI-generated title recommendations for pages
     */
    public function up(): void
    {
        Schema::create('title_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relation to Page or LocationPage
            $table->morphs('recommendable'); // recommendable_type, recommendable_id
            
            $table->string('current_title');
            $table->string('suggested_title');
            $table->text('reasoning')->nullable();
            $table->decimal('confidence_score', 5, 2)->default(0); // 0-100
            
            // Status: pending, approved, rejected, applied, rolled_back
            $table->string('status')->default('pending');
            
            // Performance data
            $table->json('current_performance')->nullable(); // CTR, impressions, etc.
            $table->json('predicted_impact')->nullable(); // Expected improvements
            $table->json('actual_impact')->nullable(); // Measured after application
            
            // AI/Generation metadata
            $table->string('generation_method')->nullable(); // ai, rule_based, hybrid
            $table->json('generation_metadata')->nullable(); // Model used, prompt, etc.
            
            // Workflow timestamps
            $table->timestamp('generated_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('measurement_completed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['site_id', 'status']);
            $table->index(['recommendable_type', 'recommendable_id', 'status']);
            $table->index('confidence_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_recommendations');
    }
};
