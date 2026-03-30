<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('business_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('industry')->nullable();
            $table->string('role_at_company')->nullable();
            $table->string('primary_market')->nullable();
            $table->json('services')->nullable();
            $table->string('top_goal')->nullable();
            $table->text('biggest_challenge')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
