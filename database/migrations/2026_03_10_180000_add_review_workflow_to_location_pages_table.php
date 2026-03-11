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
        Schema::table('location_pages', function (Blueprint $table) {
            // Review workflow fields
            $table->boolean('needs_review')->default(true)->after('status');
            $table->text('review_notes')->nullable()->after('needs_review');
            $table->string('content_quality_status')->default('unreviewed')->after('review_notes'); // unreviewed, edited, approved, excluded
            $table->timestamp('approved_at')->nullable()->after('content_quality_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            
            // Indexes for efficient filtering
            $table->index('needs_review');
            $table->index('content_quality_status');
            $table->index(['content_quality_status', 'needs_review']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_pages', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['location_pages_needs_review_index']);
            $table->dropIndex(['location_pages_content_quality_status_index']);
            $table->dropIndex(['location_pages_content_quality_status_needs_review_index']);
            $table->dropColumn(['needs_review', 'review_notes', 'content_quality_status', 'approved_at', 'approved_by']);
        });
    }
};
