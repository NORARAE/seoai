<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Force-fix the bookings.status CHECK constraint to include 'awaiting_payment'.
     *
     * The earlier migration (2026_03_30_205311) was marked as Ran but its rebuild
     * was silently reverted by a subsequent migration that used ->after() on SQLite,
     * which rewrites the table using the original Blueprint column definitions.
     * This migration re-applies the fix using the full current column list.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','awaiting_payment') NOT NULL DEFAULT 'pending'");
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='bookings'");
        $ddl = $row ? $row->sql : '';

        if (str_contains($ddl, "'awaiting_payment'")) {
            return; // Already correct
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE "bookings_fix" (
                "id"                          integer primary key autoincrement not null,
                "consult_type_id"             integer not null,
                "name"                        varchar not null,
                "email"                       varchar not null,
                "phone"                       varchar,
                "company"                     varchar,
                "website"                     varchar,
                "message"                     text,
                "preferred_date"              date not null,
                "preferred_time"              time not null,
                "status"                      varchar check ("status" in (\'pending\', \'confirmed\', \'cancelled\', \'completed\', \'awaiting_payment\')) not null default \'pending\',
                "google_event_id"             varchar,
                "google_meet_link"            varchar,
                "confirmed_at"                datetime,
                "cancelled_at"                datetime,
                "created_at"                  datetime,
                "updated_at"                  datetime,
                "stripe_checkout_session_id"  varchar,
                "stripe_payment_intent_id"    varchar,
                "reminder_sent_at"            datetime,
                "sms_opted_out"               tinyint(1) not null default \'0\',
                "reminder_24h_sent_at"        datetime,
                "reminder_2h_sent_at"         datetime,
                "public_booking_token"        varchar,
                "reschedule_count"            integer not null default \'0\',
                "last_rescheduled_at"         datetime,
                foreign key("consult_type_id") references "consult_types"("id") on delete cascade
            )
        ');

        DB::statement('INSERT INTO "bookings_fix" SELECT * FROM "bookings"');
        DB::statement('DROP TABLE "bookings"');
        DB::statement('ALTER TABLE "bookings_fix" RENAME TO "bookings"');
        DB::statement('CREATE INDEX "bookings_preferred_date_status_index" ON "bookings" ("preferred_date", "status")');
        DB::statement('CREATE UNIQUE INDEX "bookings_public_booking_token_unique" ON "bookings" ("public_booking_token")');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Removing awaiting_payment from the constraint would break existing rows
        // in that status — intentionally left as no-op.
    }
};
