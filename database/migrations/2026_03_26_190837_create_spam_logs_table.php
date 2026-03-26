<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 120);          // e.g. "honeypot_triggered", "high_risk_score", "duplicate_email"
            $table->string('spam_risk', 10);         // high|medium
            $table->float('risk_score')->default(0);
            $table->ipAddress('ip_address')->nullable();
            $table->string('email', 255)->nullable()->index();
            $table->json('signals')->nullable();     // raw signal breakdown snapshot
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spam_logs');
    }
};
