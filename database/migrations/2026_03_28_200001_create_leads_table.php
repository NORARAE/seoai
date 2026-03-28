<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();

            // Core contact
            $table->string('name');
            $table->string('email')->index();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();

            // CRM fields
            $table->string('session_type')->nullable();   // ConsultType name
            $table->string('payment_status')->nullable();  // free | paid | null
            $table->string('source')->default('booking');  // booking | referral | manual

            // Onboarding pipeline
            $table->text('notes')->nullable();             // internal admin notes

            $table->timestamps();

            $table->index('booking_id');
        });

        // Add onboarding_status as enum; SQLite-safe approach
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE leads ADD COLUMN onboarding_status ENUM('pending','submitted','approved','rejected') NOT NULL DEFAULT 'pending' AFTER source");
        } else {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('onboarding_status')->default('pending')->after('source');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
