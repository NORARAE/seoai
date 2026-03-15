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
        Schema::create('publishing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payload_id')->constrained('page_payloads')->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            $table->string('adapter_type'); // 'wordpress', 'export', etc.
            $table->enum('action', ['publish', 'update', 'delete', 'export'])->default('publish');
            $table->enum('result', ['success', 'failure', 'partial'])->default('success');
            
            $table->text('error_message')->nullable();
            $table->json('remote_response')->nullable();
            $table->json('request_data')->nullable();
            
            $table->string('remote_id')->nullable();
            $table->string('remote_url', 500)->nullable();
            
            $table->timestamps();
            
            $table->index(['payload_id', 'created_at']);
            $table->index(['site_id', 'result']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publishing_logs');
    }
};
