<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Add 'no_show' to the bookings.status constraint on both MySQL and SQLite.
     *
     * On fresh installs: create_bookings_table already includes no_show,
     *                    so this migration is a fast no-op.
     * On existing installs: MySQL receives an idempotent MODIFY COLUMN;
     *                       SQLite requires a table rebuild.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','awaiting_payment','no_show') NOT NULL DEFAULT 'pending'");
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name='bookings'");
        $ddl = $row ? $row->sql : '';

        if (str_contains($ddl, "'no_show'")) {
            return; // Already correct
        }

        // Determine which optional columns exist in the current table.
        $columns = collect(DB::select("PRAGMA table_info('bookings')"))->pluck('name');

        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE "bookings_noshw" (
                "id"                          integer primary key autoincrement not null,
                "consult_type_id"             integer not null,
                "booking_type"                varchar,
                "name"                        varchar not null,
                "email"                       varchar not null,
                "phone"                       varchar,
                "company"                     varchar,
                "website"                     varchar,
                "message"                     text,
                "add_ons"                     text,
                "preferred_date"              date not null,
                "preferred_time"              time not null,
                "status"                      varchar check ("status" in (\'pending\', \'confirmed\', \'cancelled\', \'completed\', \'awaiting_payment\', \'no_show\')) not null default \'pending\',
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
                "activation_date"             datetime,
                "cycle_end_date"              datetime,
                "deployment_status"           varchar,
                foreign key("consult_type_id") references "consult_types"("id") on delete cascade
            )
        ');

        // Build a named-column INSERT to handle any ordering differences.
        $targetCols = [
            'id',
            'consult_type_id',
            'booking_type',
            'name',
            'email',
            'phone',
            'company',
            'website',
            'message',
            'add_ons',
            'preferred_date',
            'preferred_time',
            'status',
            'google_event_id',
            'google_meet_link',
            'confirmed_at',
            'cancelled_at',
            'created_at',
            'updated_at',
            'stripe_checkout_session_id',
            'stripe_payment_intent_id',
            'reminder_sent_at',
            'sms_opted_out',
            'reminder_24h_sent_at',
            'reminder_2h_sent_at',
            'public_booking_token',
            'reschedule_count',
            'last_rescheduled_at',
            'activation_date',
            'cycle_end_date',
            'deployment_status',
        ];

        // Only copy columns that actually exist in the old table.
        $existing = $columns->intersect($targetCols)->values()->toArray();
        $colList = implode(', ', array_map(fn($c) => '"' . $c . '"', $existing));

        DB::statement("INSERT INTO \"bookings_noshw\" ({$colList}) SELECT {$colList} FROM \"bookings\"");
        DB::statement('DROP TABLE "bookings"');
        DB::statement('ALTER TABLE "bookings_noshw" RENAME TO "bookings"');
        DB::statement('CREATE INDEX "bookings_preferred_date_status_index" ON "bookings" ("preferred_date", "status")');

        if ($columns->contains('public_booking_token')) {
            DB::statement('CREATE UNIQUE INDEX "bookings_public_booking_token_unique" ON "bookings" ("public_booking_token")');
        }

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE bookings SET status = 'pending' WHERE status = 'no_show'");
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','awaiting_payment') NOT NULL DEFAULT 'pending'");
        }
        // SQLite: intentional no-op — removing no_show would break rows in that status.
    }
};
