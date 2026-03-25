<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_domains', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('domain');
            $table->timestamps();

            $table->unique(['site_id', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_domains');
    }
};
