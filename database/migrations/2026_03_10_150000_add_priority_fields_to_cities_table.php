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
        Schema::table('cities', function (Blueprint $table) {
            $table->boolean('is_county_seat')->default(false)->after('population');
            $table->boolean('is_priority')->default(false)->after('is_county_seat');
            
            $table->index('is_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex(['is_priority']);
            $table->dropColumn(['is_county_seat', 'is_priority']);
        });
    }
};
