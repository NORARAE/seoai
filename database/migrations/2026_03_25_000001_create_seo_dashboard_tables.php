<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_reports', function (Blueprint $table) {
            $table->id();
            $table->string('site_url');
            $table->string('report_type');  // gsc|ga4
            $table->string('dimension');
            $table->string('date_range');
            $table->json('data');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['report_type', 'dimension', 'date_range']);
        });

        Schema::create('seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->float('ctr')->default(0);
            $table->float('position')->default(0);
            $table->string('date_range');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['date_range', 'clicks']);
        });

        Schema::create('seo_traffic', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->integer('sessions')->default(0);
            $table->integer('users')->default(0);
            $table->integer('pageviews')->default(0);
            $table->float('bounce_rate')->default(0);
            $table->string('date_range');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['date_range', 'sessions']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_traffic');
        Schema::dropIfExists('seo_keywords');
        Schema::dropIfExists('seo_reports');
    }
};
