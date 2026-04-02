<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── onboarding_submissions: qualifying + ads fields ────────────────────
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->text('goals')->nullable()->after('service_area');
            $table->text('challenges')->nullable()->after('goals');
            $table->string('growth_intent')->nullable()->after('challenges'); // aggressive|steady|unsure
            $table->string('ads_status')->nullable()->after('growth_intent');  // running|has_budget|no_budget|not_interested
        });

        // ── leads: tags for pipeline tracking ─────────────────────────────────
        Schema::table('leads', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('lifecycle_stage');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn(['goals', 'challenges', 'growth_intent', 'ads_status']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
