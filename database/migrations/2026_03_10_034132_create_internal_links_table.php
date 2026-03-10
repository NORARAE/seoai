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
        Schema::create('internal_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_page_id')->nullable()->constrained('pages')->onDelete('cascade');
            $table->string('source_url');
            $table->string('target_url');
            $table->text('anchor_text')->nullable();
            $table->timestamps();

            // Prevent duplicate links: same source, target, and anchor text per site
            $table->unique(['site_id', 'source_url', 'target_url', 'anchor_text'], 'unique_internal_link');
            
            // Index for efficient queries
            $table->index(['site_id', 'source_url']);
            $table->index(['site_id', 'target_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_links');
    }
};
