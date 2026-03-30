<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->after('cancelled_at');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
        });

        // MySQL / MariaDB: ensure awaiting_payment is included in the ENUM.
        // On a fresh install this is redundant (create migration is authoritative) but
        // required for any existing deployment whose table pre-dates this migration.
        // SQLite: the create migration already includes awaiting_payment in the CHECK
        // constraint, and 2026_03_30_205311 handles the fix for older SQLite databases.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','awaiting_payment') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['stripe_checkout_session_id', 'stripe_payment_intent_id']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE bookings SET status = 'pending' WHERE status = 'awaiting_payment'");
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending'");
        }
    }
};
