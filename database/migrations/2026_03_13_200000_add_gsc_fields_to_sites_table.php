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
            $table->string('gsc_property_url', 500)->nullable()->after('last_crawled_at');
            $table->text('gsc_access_token')->nullable()->after('gsc_property_url');
            $table->text('gsc_refresh_token')->nullable()->after('gsc_access_token');
            $table->timestamp('gsc_token_expires_at')->nullable()->after('gsc_refresh_token');
            $table->timestamp('gsc_last_sync_at')->nullable()->after('gsc_token_expires_at');
            $table->enum('gsc_sync_status', ['pending', 'syncing', 'completed', 'failed'])
                ->default('pending')
                ->after('gsc_last_sync_at');
            $table->text('gsc_sync_error')->nullable()->after('gsc_sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'gsc_property_url',
                'gsc_access_token',
                'gsc_refresh_token',
                'gsc_token_expires_at',
                'gsc_last_sync_at',
                'gsc_sync_status',
                'gsc_sync_error',
            ]);
        });
    }
};
