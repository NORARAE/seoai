<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('raw_checks');
            $table->unsignedInteger('page_count')->nullable()->after('categories');
            $table->json('broken_links')->nullable()->after('page_count');
        });
    }

    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropColumn(['categories', 'page_count', 'broken_links']);
        });
    }
};
