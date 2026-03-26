<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('company', 120);
            $table->string('email', 255)->index();
            $table->string('website', 255)->nullable();
            $table->string('type', 30);           // agency | business | both
            $table->string('tier', 30);           // starter | 5k | 10k | legacy
            $table->string('niche', 255)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 30)->default('new')->index(); // new | contacted | converted | spam
            $table->timestamp('welcome_sent_at')->nullable();
            $table->timestamp('admin_notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
