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
        Schema::create('page_payloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->nullable()->constrained('page_generation_batches')->nullOnDelete();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->nullable(); // Polymorphic: City, County, State
            $table->string('location_type')->nullable(); // 'city', 'county', 'state'

            // Core content
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->string('slug')->index();
            $table->string('canonical_url_suggestion')->nullable();
            $table->longText('body_content')->nullable();
            $table->text('excerpt')->nullable();

            // SEO assets
            $table->json('schema_json_ld')->nullable();
            $table->string('structured_data_type')->nullable(); // 'LocalBusiness', 'Service', etc.
            $table->string('og_image_url')->nullable();
            $table->json('og_tags')->nullable();

            // Linking strategy
            $table->json('internal_link_suggestions')->nullable();
            $table->json('anchor_text_suggestions')->nullable();
            $table->json('outbound_links')->nullable();

            // Hierarchy
            $table->string('parent_page_slug')->nullable();
            $table->string('hub_page_slug')->nullable();
            $table->json('related_pages')->nullable();
            $table->json('submenu_suggestions')->nullable();

            // Sitemap metadata
            $table->decimal('sitemap_priority', 3, 2)->default(0.5);
            $table->enum('sitemap_changefreq', ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])->default('monthly');
            $table->timestamp('sitemap_lastmod')->nullable();

            // Publishing metadata
            $table->text('publish_notes')->nullable();
            $table->enum('publish_status', ['pending', 'published', 'exported', 'failed'])->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->string('remote_id')->nullable(); // WordPress post ID, Wix page ID, etc.
            $table->string('remote_url', 500)->nullable();
            $table->string('remote_edit_url', 500)->nullable();

            // Quality scores
            $table->decimal('content_quality_score', 5, 2)->nullable();
            $table->decimal('seo_score', 5, 2)->nullable();
            $table->decimal('readability_score', 5, 2)->nullable();

            // Generation metadata
            $table->string('generated_by')->nullable(); // Service/job class name
            $table->json('generation_params')->nullable();
            $table->string('template_used')->nullable();
            $table->string('ai_model_used')->nullable();

            $table->enum('status', ['draft', 'ready', 'published', 'archived'])->default('draft');
            $table->timestamps();

            // Indexes
            $table->index(['site_id', 'status']);
            $table->index(['batch_id', 'publish_status']);
            $table->index(['service_id', 'location_id']);
            $table->unique(['site_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_payloads');
    }
};
