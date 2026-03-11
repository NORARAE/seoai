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
            $table->boolean('is_indexable')->default(true)->after('status');
            
            $table->index('is_indexable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_pages', function (Blueprint $table) {
            $table->dropIndex(['is_indexable']);
            $table->dropColumn('is_indexable');
        });
    }
};
