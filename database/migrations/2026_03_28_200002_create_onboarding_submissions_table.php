<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();

            // Business info
            $table->string('business_name');
            $table->string('website')->nullable();
            $table->text('service_area')->nullable();

            // Business license upload (stored in storage/app/private — never public)
            $table->string('license_path')->nullable();
            $table->string('license_original_name')->nullable();
            $table->unsignedBigInteger('license_size_bytes')->nullable();
            $table->string('license_mime')->nullable();

            // Primary contact
            $table->string('primary_contact');
            $table->string('phone', 50);

            // Ad readiness
            $table->boolean('ad_budget_ready')->default(false);
            $table->string('payment_method_for_ads')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_submissions');
    }
};
