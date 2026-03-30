<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Signup provenance — captured once at account creation.
            // Privacy note: IP and UA are stored for fraud/abuse prevention only.
            $table->string('signup_ip', 45)->nullable()->after('auth_provider');
            $table->string('signup_user_agent', 512)->nullable()->after('signup_ip');
            $table->string('signup_referrer', 512)->nullable()->after('signup_user_agent');
            // 'web-register' | 'google-oauth'
            $table->string('signup_source', 32)->nullable()->default('web-register')->after('signup_referrer');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['signup_ip', 'signup_user_agent', 'signup_referrer', 'signup_source']);
        });
    }
};
