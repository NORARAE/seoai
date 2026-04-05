<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->string('years_in_business', 20)->nullable()->after('add_ons');
            $table->string('team_size', 20)->nullable()->after('years_in_business');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn(['years_in_business', 'team_size']);
        });
    }
};
