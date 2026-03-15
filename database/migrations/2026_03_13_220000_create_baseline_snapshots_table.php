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
        Schema::create('baseline_snapshots', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship - can snapshot Page or LocationPage
            $table->string('snapshotable_type');
            $table->unsignedBigInteger('snapshotable_id');
            
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            $table->timestamp('snapshot_date');
            
            // Page content at time of snapshot
            $table->text('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('h1')->nullable();
            $table->text('canonical_url')->nullable();
            
            // Hashes for change detection
            $table->char('content_hash', 64)->nullable(); // SHA256
            $table->char('rendered_html_hash', 64)->nullable(); // For LocationPages with cached HTML
            
            // Schema snapshot
            $table->json('schema_json')->nullable();
            
            // Performance snapshot - 30-day aggregates before this snapshot
            // Example: {"clicks": 450, "impressions": 12500, "ctr": 0.036, "avg_position": 8.2}
            $table->json('performance_snapshot_json')->nullable();
            
            $table->timestamp('created_at');
            
            // Indexes
            $table->index(['snapshotable_type', 'snapshotable_id']);
            $table->index(['site_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baseline_snapshots');
    }
};
