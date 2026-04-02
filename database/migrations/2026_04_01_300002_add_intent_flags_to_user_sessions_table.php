<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->boolean('visited_book')->default(false)->after('last_activity_at');
            $table->boolean('opened_booking_modal')->default(false)->after('visited_book');
            $table->boolean('visited_onboarding')->default(false)->after('opened_booking_modal');
        });
    }

    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropColumn(['visited_book', 'opened_booking_modal', 'visited_onboarding']);
        });
    }
};
