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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('path')->nullable();
            $table->string('title')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('crawl_status')->default('discovered');
            $table->timestamp('last_crawled_at')->nullable();
            $table->timestamps();

            // Composite unique constraint: same URL can't exist twice for the same site
            $table->unique(['site_id', 'url']);
            
            // Index for efficient queries
            $table->index(['site_id', 'crawl_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
