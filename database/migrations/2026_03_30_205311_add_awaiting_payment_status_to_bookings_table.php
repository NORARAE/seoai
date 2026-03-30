<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ensure 'awaiting_payment' is a valid status on both MySQL and SQLite.
     *
     * On fresh installs:  create_bookings_table already includes awaiting_payment,
     *                     so this migration is a fast no-op.
     * On existing installs: MySQL receives an idempotent MODIFY COLUMN; SQLite
     *                       requires a table rebuild because CHECK constraints
     *                       cannot be altered in place.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Idempotent — safe to run even if awaiting_payment is already present.
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','awaiting_payment') NOT NULL DEFAULT 'pending'");
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            // Read the current CREATE TABLE DDL from sqlite_master.
            $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='bookings'");
            $ddl = $row ? $row->sql : '';

            // If awaiting_payment is already in the CHECK constraint, nothing to do.
            if (str_contains($ddl, "'awaiting_payment'")) {
                return;
            }

            // SQLite cannot ALTER a CHECK constraint — rebuild the table safely.
            // All existing rows are preserved; only the constraint definition changes.
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement('
                CREATE TABLE "bookings_new" (
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
                    foreign key("consult_type_id") references "consult_types"("id") on delete cascade
                )
            ');

            DB::statement('INSERT INTO "bookings_new" SELECT * FROM "bookings"');
            DB::statement('DROP TABLE "bookings"');
            DB::statement('ALTER TABLE "bookings_new" RENAME TO "bookings"');
            DB::statement('CREATE INDEX "bookings_preferred_date_status_index" ON "bookings" ("preferred_date", "status")');

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Reverse the migration — remove awaiting_payment from the status constraint.
     * Rows in that status are reset to pending first to avoid orphaning data.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE bookings SET status = 'pending' WHERE status = 'awaiting_payment'");
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending'");
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='bookings'");
            $ddl = $row ? $row->sql : '';

            // If awaiting_payment was never in the constraint, nothing to roll back.
            if (!str_contains($ddl, "'awaiting_payment'")) {
                return;
            }

            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement("UPDATE \"bookings\" SET status = 'pending' WHERE status = 'awaiting_payment'");

            DB::statement('
                CREATE TABLE "bookings_new" (
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
                    "status"                      varchar check ("status" in (\'pending\', \'confirmed\', \'cancelled\', \'completed\')) not null default \'pending\',
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
                    foreign key("consult_type_id") references "consult_types"("id") on delete cascade
                )
            ');

            DB::statement('INSERT INTO "bookings_new" SELECT * FROM "bookings"');
            DB::statement('DROP TABLE "bookings"');
            DB::statement('ALTER TABLE "bookings_new" RENAME TO "bookings"');
            DB::statement('CREATE INDEX "bookings_preferred_date_status_index" ON "bookings" ("preferred_date", "status")');

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
