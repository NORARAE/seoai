<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 120)->nullable()->index();
            $table->unsignedBigInteger('scan_id')->nullable()->index();
            $table->string('domain')->nullable()->index();
            $table->text('user_message');
            $table->text('ai_response')->nullable();
            $table->string('intent', 40)->nullable()->index();
            $table->string('context_page', 40)->default('landing')->index();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_logs');
    }
};
