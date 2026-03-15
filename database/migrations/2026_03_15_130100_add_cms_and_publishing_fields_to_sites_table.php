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
        Schema::table('sites', function (Blueprint $table) {
            // CMS configuration
            $table->enum('cms_type', ['wordpress', 'wix', 'squarespace', 'webflow', 'shopify', 'custom', 'unknown'])
                ->default('unknown')
                ->after('domain');
            
            $table->enum('publishing_mode', ['native', 'export_only', 'api', 'manual'])
                ->default('export_only')
                ->after('cms_type');
            
            $table->enum('publishing_status', ['connected', 'partial', 'manual', 'error'])
                ->default('manual')
                ->after('publishing_mode');

            // WordPress-specific fields (encrypted)
            $table->string('wordpress_url', 500)->nullable()->after('publishing_status');
            $table->string('wordpress_username')->nullable()->after('wordpress_url');
            $table->text('wordpress_app_password')->nullable()->after('wordpress_username'); // Encrypted
            
            // Generic API fields
            $table->string('api_endpoint', 500)->nullable()->after('wordpress_app_password');
            $table->text('api_credentials')->nullable()->after('api_endpoint'); // Encrypted JSON

            // Connection testing
            $table->timestamp('last_connection_test_at')->nullable()->after('api_credentials');
            $table->string('connection_test_status')->nullable()->after('last_connection_test_at');
            $table->text('connection_test_error')->nullable()->after('connection_test_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'cms_type',
                'publishing_mode',
                'publishing_status',
                'wordpress_url',
                'wordpress_username',
                'wordpress_app_password',
                'api_endpoint',
                'api_credentials',
                'last_connection_test_at',
                'connection_test_status',
                'connection_test_error',
            ]);
        });
    }
};
