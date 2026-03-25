<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table): void {
            $table->id();
            $table->string('license_key', 32)->unique();
            $table->string('customer_email');
            $table->string('customer_name');
            $table->string('site_url');
            $table->string('plan');
            $table->unsignedInteger('urls_allowed')->nullable();
            $table->string('stripe_subscription_id')->nullable()->index();
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('status');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index('site_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};