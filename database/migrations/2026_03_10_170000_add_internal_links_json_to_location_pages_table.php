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
            $table->json('internal_links_json')->nullable()->after('body_sections_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_pages', function (Blueprint $table) {
            $table->dropColumn('internal_links_json');
        });
    }
};
