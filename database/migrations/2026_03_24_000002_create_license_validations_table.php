<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_validations', function (Blueprint $table): void {
            $table->id();
            $table->string('license_key', 32);
            $table->string('site_url')->nullable();
            $table->string('plugin_ver')->nullable();
            $table->string('result');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['license_key', 'created_at']);
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_validations');
    }
};