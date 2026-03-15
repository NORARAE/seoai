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
        Schema::create('optimization_runs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relationship - can optimize Page or LocationPage
            $table->string('optimizable_type');
            $table->unsignedBigInteger('optimizable_id');
            
            // Type of optimization
            $table->enum('optimization_type', [
                'title',
                'meta_description',
                'content',
                'schema',
                'links',
                'other',
            ]);
            
            // Status lifecycle
            $table->enum('status', [
                'detected',
                'recommended',
                'approved',
                'applied',
                'monitoring',
                'succeeded',
                'failed',
                'rolled_back',
            ])->default('detected');
            
            // Confidence in this optimization (0-100)
            $table->unsignedTinyInteger('confidence_score')->nullable();
            
            // Was this auto-applied or manually approved?
            $table->boolean('auto_applied')->default(false);
            
            // Who approved it (if manual)
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Link to baseline snapshot taken before optimization
            $table->foreignId('baseline_snapshot_id')->nullable()->constrained('baseline_snapshots')->onDelete('set null');
            
            // State storage as JSON for flexibility
            $table->json('before_state_json')->nullable(); // What it was
            $table->json('proposed_state_json')->nullable(); // What we recommended
            $table->json('applied_state_json')->nullable(); // What was actually applied (may differ from proposed)
            
            // Impact tracking
            $table->json('predicted_impact_json')->nullable(); // Expected improvements
            $table->json('actual_impact_json')->nullable(); // Measured improvements after monitoring
            
            // Monitoring window
            $table->timestamp('monitoring_started_at')->nullable();
            $table->timestamp('monitoring_ends_at')->nullable();
            
            // Success criteria for this optimization
            $table->json('success_criteria_json')->nullable();
            
            // Rollback tracking
            $table->text('rollback_reason')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['optimizable_type', 'optimizable_id']);
            $table->index(['site_id', 'status', 'optimization_type']);
            $table->index(['status', 'monitoring_ends_at']); // For finding runs that need monitoring
            $table->index(['auto_applied', 'status']); // For trust score calculations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimization_runs');
    }
};
