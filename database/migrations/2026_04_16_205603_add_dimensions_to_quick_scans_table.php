<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->json('dimensions')->nullable()->after('broken_links');
            $table->json('intelligence')->nullable()->after('dimensions');
        });
    }

    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropColumn(['dimensions', 'intelligence']);
        });
    }
};
