<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            // Captures scale signal for multi-location/agency lead routing
            // Values: '1', '2_to_5', '6_to_10', '11_to_20', '20_plus'
            $table->string('number_of_locations', 20)->nullable()->after('lead_type');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn('number_of_locations');
        });
    }
};
