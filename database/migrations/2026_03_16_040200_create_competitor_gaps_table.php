<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_gaps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('keyword_topic');
            $table->unsignedInteger('search_volume')->default(0);
            $table->string('competitor_domain');
            $table->string('competitor_url', 2048)->nullable();
            $table->boolean('page_missing')->default(true);
            $table->unsignedInteger('opportunity_score')->default(0);
            $table->enum('score_label', ['high', 'medium', 'low'])->default('low');
            $table->enum('status', ['open', 'queued', 'generated', 'ignored'])->default('open');
            $table->json('evidence')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'status']);
            $table->index(['site_id', 'opportunity_score']);
            $table->unique(['site_id', 'keyword_topic', 'competitor_domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_gaps');
    }
};
