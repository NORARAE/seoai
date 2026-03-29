<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Valid lifecycle stages (in order of progression):
     *   new → booked → paid → onboarding_submitted → approved → active
     *   (terminal: rejected, lost)
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lifecycle_stage')->default('new')->after('source');
        });

        // Backfill existing records from their current onboarding_status
        // so historical data is coherent after the migration.
        DB::statement("
            UPDATE leads SET lifecycle_stage =
            CASE
                WHEN onboarding_status = 'approved'  THEN 'approved'
                WHEN onboarding_status = 'submitted' THEN 'onboarding_submitted'
                WHEN onboarding_status = 'rejected'  THEN 'rejected'
                WHEN payment_status   = 'paid'       THEN 'paid'
                WHEN payment_status   = 'free'       THEN 'booked'
                ELSE 'new'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('lifecycle_stage');
        });
    }
};
