<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('url_inventory', function (Blueprint $table): void {
            $table->unsignedInteger('internal_link_count')->default(0)->after('word_count')
                ->comment('Number of internal outbound links from this page');
            $table->unsignedInteger('incoming_link_count')->default(0)->after('internal_link_count')
                ->comment('Number of internal links pointing to this page');
            $table->boolean('is_orphan_page')->default(true)->after('incoming_link_count')
                ->comment('True when no other page links to this URL');
        });
    }

    public function down(): void
    {
        Schema::table('url_inventory', function (Blueprint $table): void {
            $table->dropColumn(['internal_link_count', 'incoming_link_count', 'is_orphan_page']);
        });
    }
};
