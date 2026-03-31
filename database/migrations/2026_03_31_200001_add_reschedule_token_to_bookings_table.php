<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('public_booking_token', 64)->nullable()->unique()->after('id');
            $table->unsignedTinyInteger('reschedule_count')->default(0)->after('cancelled_at');
            $table->timestamp('last_rescheduled_at')->nullable()->after('reschedule_count');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['public_booking_token', 'reschedule_count', 'last_rescheduled_at']);
        });
    }
};
