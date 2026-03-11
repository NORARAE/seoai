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
            // Render cache fields
            $table->string('render_version')->nullable()->after('generated_at');
            $table->longText('rendered_html_cache')->nullable()->after('render_version');
            $table->text('rendered_excerpt_cache')->nullable()->after('rendered_html_cache');
            
            // Schema cache fields
            $table->json('faq_schema_json')->nullable()->after('internal_links_json');
            $table->json('service_schema_json')->nullable()->after('faq_schema_json');
            $table->json('local_business_schema_json')->nullable()->after('service_schema_json');
            $table->json('schema_cache_json')->nullable()->after('local_business_schema_json');
            
            // Timestamp for when rendering occurred
            $table->timestamp('rendered_at')->nullable()->after('rendered_excerpt_cache');
            
            // Track if page needs re-rendering (dirty flag)
            $table->boolean('needs_render')->default(true)->after('rendered_at');
            
            // Index for querying pages that need rendering
            $table->index(['needs_render', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_pages', function (Blueprint $table) {
            $table->dropIndex(['needs_render', 'status']);
            $table->dropColumn([
                'render_version',
                'rendered_html_cache',
                'rendered_excerpt_cache',
                'faq_schema_json',
                'service_schema_json',
                'local_business_schema_json',
                'schema_cache_json',
                'rendered_at',
                'needs_render',
            ]);
        });
    }
};
