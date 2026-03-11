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
        Schema::create('link_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('target_page_id')->constrained('pages')->onDelete('cascade');
            $table->string('suggested_anchor_text');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();

            // Prevent duplicate suggestions
            $table->unique(['source_page_id', 'target_page_id'], 'unique_link_suggestion');
            
            // Index for queries
            $table->index(['site_id', 'status']);
            $table->index('target_page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_suggestions');
    }
};
