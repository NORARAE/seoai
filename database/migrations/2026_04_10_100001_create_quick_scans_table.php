<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quick_scans', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('url');
            $table->string('stripe_session_id')->nullable()->index();
            $table->boolean('paid')->default(false);
            $table->integer('score')->nullable();
            $table->json('issues')->nullable();
            $table->json('strengths')->nullable();
            $table->string('fastest_fix')->nullable();
            $table->json('raw_checks')->nullable();
            $table->string('status')->default('pending'); // pending|paid|scanned|error
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_scans');
    }
};
