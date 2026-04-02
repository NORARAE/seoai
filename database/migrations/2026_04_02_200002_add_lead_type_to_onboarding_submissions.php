<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add lead_type to onboarding_submissions for multi-site flag (Part 8)
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->string('lead_type', 50)->nullable()->after('rd_referral_interest');
            // Possible values: 'single_location', 'multi_location', 'agency'
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn('lead_type');
        });
    }
};
