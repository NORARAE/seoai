<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Timestamp set when reminder SMS is dispatched
            $table->timestamp('reminder_sent_at')->nullable()->after('stripe_payment_intent_id');
            // Whether the client opted out of SMS reminders
            $table->boolean('sms_opted_out')->default(false)->after('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'sms_opted_out']);
        });
    }
};
