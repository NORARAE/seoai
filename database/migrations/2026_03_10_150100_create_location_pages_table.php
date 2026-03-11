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
        Schema::create('location_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'county_hub' or 'service_city'
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->foreignId('county_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('parent_location_page_id')->nullable()->constrained('location_pages')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('url_path')->unique();
            $table->string('title');
            $table->string('meta_title');
            $table->text('meta_description');
            $table->string('h1');
            $table->string('canonical_url');
            $table->json('body_sections_json')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            // Indexes for efficient queries
            $table->index('type');
            $table->index('status');
            $table->index(['state_id', 'type']);
            $table->index(['county_id', 'type']);
            $table->index(['service_id', 'city_id']);
            $table->index('score');
            
            // Unique constraint to prevent duplicate pages
            $table->unique(['type', 'county_id', 'city_id', 'service_id'], 'unique_location_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_pages');
    }
};
