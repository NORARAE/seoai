<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            // Access setup
            $table->boolean('analytics_access')->default(false)->after('payment_method_for_ads');
            $table->boolean('search_console_access')->default(false)->after('analytics_access');
            $table->string('platform_type', 50)->nullable()->after('search_console_access');

            // Access method workflow
            $table->string('access_method', 50)->nullable()->after('platform_type');

            // Upsell selections (array of add-on slugs)
            $table->json('add_ons')->nullable()->after('access_method');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'analytics_access',
                'search_console_access',
                'platform_type',
                'access_method',
                'add_ons',
            ]);
        });
    }
};
