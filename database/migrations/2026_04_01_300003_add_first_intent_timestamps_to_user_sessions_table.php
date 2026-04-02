<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->timestamp('first_book_at')->nullable()->after('visited_onboarding');
            $table->timestamp('first_modal_open_at')->nullable()->after('first_book_at');
            $table->timestamp('first_onboarding_at')->nullable()->after('first_modal_open_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropColumn(['first_book_at', 'first_modal_open_at', 'first_onboarding_at']);
        });
    }
};
