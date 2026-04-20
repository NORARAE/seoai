<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_marketing_opt_in')->default(true)->after('stripe_checkout_session_id');
            $table->boolean('email_product_updates')->default(true)->after('email_marketing_opt_in');
            $table->boolean('email_scan_notifications')->default(true)->after('email_product_updates');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_marketing_opt_in', 'email_product_updates', 'email_scan_notifications']);
        });
    }
};
