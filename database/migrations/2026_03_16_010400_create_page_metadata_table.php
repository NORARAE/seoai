<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->unique()->constrained('url_inventory')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical', 2048)->nullable();
            $table->string('h1')->nullable();
            $table->json('h2s')->nullable();
            $table->text('meta_robots')->nullable();
            $table->json('schema')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_metadata');
    }
};
