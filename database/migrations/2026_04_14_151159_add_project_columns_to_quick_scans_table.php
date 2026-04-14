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
            // Per-scan upgrade tracking
            $table->string('upgrade_plan')->nullable()->after('status');       // citation-builder | authority-engine
            $table->string('upgrade_status')->nullable()->after('upgrade_plan'); // pending | paid | active | completed
            $table->string('upgrade_stripe_session_id')->nullable()->after('upgrade_status');
            $table->timestamp('upgraded_at')->nullable()->after('upgrade_stripe_session_id');

            // Per-scan onboarding link
            $table->foreignId('onboarding_submission_id')->nullable()->after('upgraded_at')
                ->constrained('onboarding_submissions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('onboarding_submission_id');
            $table->dropColumn(['upgrade_plan', 'upgrade_status', 'upgrade_stripe_session_id', 'upgraded_at']);
        });
    }
};
