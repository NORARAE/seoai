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
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedInteger('incoming_links_count')->default(0)->after('crawl_status');
            $table->unsignedInteger('outgoing_links_count')->default(0)->after('incoming_links_count');
            
            $table->index('incoming_links_count');
            $table->index('outgoing_links_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['incoming_links_count']);
            $table->dropIndex(['outgoing_links_count']);
            $table->dropColumn(['incoming_links_count', 'outgoing_links_count']);
        });
    }
};
