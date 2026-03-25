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
        Schema::create('page_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_id')->unique()->constrained('url_inventory')->onDelete('cascade');
            $table->longText('body_text')->nullable();
            $table->text('excerpt')->nullable();
            $table->unsignedInteger('word_count')->default(0);
            $table->decimal('readability', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_content');
    }
};
