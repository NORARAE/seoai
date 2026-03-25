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
        Schema::create('site_crawl_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->unique()->constrained()->onDelete('cascade');
            $table->unsignedInteger('max_pages')->default(2000);
            $table->unsignedInteger('crawl_delay')->default(1);
            $table->unsignedInteger('max_depth')->default(4);
            $table->boolean('obey_robots')->default(true);
            $table->boolean('follow_nofollow')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_crawl_settings');
    }
};
