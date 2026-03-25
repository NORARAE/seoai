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
        Schema::create('crawl_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('url_inventory_id')->nullable()->constrained('url_inventory')->nullOnDelete();
            $table->string('url', 2048);
            $table->unsignedInteger('priority')->default(50);
            $table->unsignedInteger('depth')->default(0);
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->string('discovered_from', 2048)->nullable();
            $table->timestamp('available_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'status', 'priority']);
            $table->index(['site_id', 'available_at']);
            $table->index(['site_id', 'depth']);
            $table->index(['site_id', 'url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_queue');
    }
};
