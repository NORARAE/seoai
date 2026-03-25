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
        Schema::create('url_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('url', 2048);
            $table->string('normalized_url', 2048);
            $table->string('path', 2048)->nullable();
            $table->unsignedInteger('depth')->default(0);
            $table->foreignId('discovered_from')->nullable()->constrained('url_inventory')->nullOnDelete();
            $table->enum('discovery_method', ['sitemap', 'crawl', 'manual', 'import'])->default('crawl');
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->timestamp('last_crawled_at')->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->unsignedInteger('word_count')->default(0);
            $table->enum('indexability_status', ['unknown', 'indexable', 'noindex', 'canonicalized', 'blocked', 'non_200'])->default('unknown');
            $table->enum('page_type', ['unknown', 'homepage', 'category', 'service', 'location', 'blog', 'landing', 'other'])->default('unknown');
            $table->unsignedInteger('crawl_priority')->default(50);
            $table->timestamps();

            $table->unique(['site_id', 'normalized_url'], 'url_inventory_site_normalized_unique');
            $table->index(['site_id', 'status']);
            $table->index(['site_id', 'page_type']);
            $table->index(['site_id', 'crawl_priority']);
            $table->index(['site_id', 'last_crawled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_inventory');
    }
};
