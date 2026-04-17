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
        Schema::table('users', function (Blueprint $table) {
            $table->string('system_tier', 30)->nullable()->after('role');
            $table->timestamp('system_tier_upgraded_at')->nullable()->after('system_tier');
            $table->string('stripe_checkout_session_id')->nullable()->after('system_tier_upgraded_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['system_tier', 'system_tier_upgraded_at', 'stripe_checkout_session_id']);
        });
    }
};
