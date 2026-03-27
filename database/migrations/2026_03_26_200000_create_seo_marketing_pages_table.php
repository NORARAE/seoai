<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_marketing_pages', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('url_slug')->unique();
            $table->string('cluster', 32)->index(); // core|agency|local|strategy|industry
            $table->string('search_intent', 32)->default('commercial'); // commercial|informational|navigational
            $table->string('nav_label')->nullable();

            // Keywords
            $table->string('primary_keyword');
            $table->json('secondary_keywords')->nullable();

            // Meta / SEO head
            $table->string('meta_title', 120);
            $table->string('meta_description', 320);
            $table->string('og_title', 120)->nullable();
            $table->string('og_description', 320)->nullable();

            // Body content
            $table->string('h1');
            $table->json('h2_structure')->nullable();     // array of h2 strings
            $table->text('hook')->nullable();
            $table->text('system_explanation')->nullable();
            $table->json('benefits')->nullable();         // array of strings
            $table->text('exclusivity')->nullable();
            $table->json('use_cases')->nullable();        // [{type, description}]
            $table->text('internal_linking_section')->nullable();

            // CTAs (stored as JSON: {text, url, anchor_text})
            $table->json('cta_top')->nullable();
            $table->json('cta_mid')->nullable();
            $table->json('cta_bottom')->nullable();

            // Internal linking
            $table->json('internal_links')->nullable(); // {homepage_ctas, lateral}

            // Schema JSON-LD (WebPage + org overlay)
            $table->json('schema_json')->nullable();

            // Sitemap
            $table->decimal('sitemap_priority', 3, 2)->default(0.70);
            $table->string('sitemap_changefreq', 16)->default('monthly');
            $table->string('sitemap_file', 64)->nullable(); // e.g. /sitemap-industry.xml

            // Ranking / editorial
            $table->unsignedSmallInteger('money_page_rank')->nullable()->index();
            $table->string('priority_level', 8)->default('medium'); // high|medium|low
            $table->boolean('is_indexed')->default(true);

            $table->timestamps();

            $table->index(['cluster', 'is_indexed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_marketing_pages');
    }
};
