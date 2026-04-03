<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            // Commitment & pricing structure
            $table->string('commitment_length', 20)->nullable()->after('number_of_locations');
            $table->string('payment_structure', 30)->nullable()->after('commitment_length');

            // Offer routing
            $table->string('offer_path', 30)->nullable()->after('payment_structure');
            $table->string('rollout_scope', 20)->nullable()->after('offer_path');

            // Flags for internal routing / admin review
            $table->boolean('agency_review_required')->default(false)->after('rollout_scope');
            $table->boolean('ads_management_required')->default(false)->after('agency_review_required');

            // Ads account ownership / access control
            $table->string('ads_account_control', 20)->nullable()->after('ads_management_required');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'commitment_length',
                'payment_structure',
                'offer_path',
                'rollout_scope',
                'agency_review_required',
                'ads_management_required',
                'ads_account_control',
            ]);
        });
    }
};
