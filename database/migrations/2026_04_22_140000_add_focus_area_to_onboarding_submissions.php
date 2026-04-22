<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            // Single-choice priority captured during onboarding to shape
            // dashboard/report emphasis. Allowed values:
            //   improve_visibility | expand_markets | generate_leads | not_sure
            $table->string('focus_area', 32)->nullable()->after('growth_intent');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn('focus_area');
        });
    }
};
