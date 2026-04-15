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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('user_type', 30)->default('individual')->after('fit_status');
            $table->unsignedSmallInteger('domain_count')->default(0)->after('user_type');
            $table->unsignedSmallInteger('scan_count')->default(0)->after('domain_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'domain_count', 'scan_count']);
        });
    }
};
