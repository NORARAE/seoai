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
        Schema::create('crawl_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->text('robots_txt')->nullable();
            $table->json('allow_rules')->nullable();
            $table->json('disallow_rules')->nullable();
            $table->json('sitemap_urls')->nullable();
            $table->unsignedInteger('crawl_delay')->default(1);
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamp('last_request_at')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'last_fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_policies');
    }
};
