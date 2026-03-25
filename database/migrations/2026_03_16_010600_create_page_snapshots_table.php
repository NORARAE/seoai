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
        Schema::create('page_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->constrained('url_inventory')->onDelete('cascade');
            $table->string('content_hash', 64);
            $table->timestamp('snapshot_date');
            $table->timestamps();

            $table->index(['url_id', 'snapshot_date']);
            $table->index(['content_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_snapshots');
    }
};
