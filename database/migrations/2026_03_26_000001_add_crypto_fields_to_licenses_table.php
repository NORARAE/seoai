<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table): void {
            // Which payment processor created this license.
            // null = Stripe (existing behavior), 'crypto' = Coinbase Commerce.
            $table->string('payment_method', 20)->nullable()->default(null)->after('stripe_customer_id');

            // Coinbase Commerce charge ID — used for idempotency and status updates.
            $table->string('crypto_charge_id', 100)->nullable()->unique()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table): void {
            $table->dropColumn(['payment_method', 'crypto_charge_id']);
        });
    }
};
