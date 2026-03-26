<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // IP Geolocation
            $table->string('ip_city', 120)->nullable()->after('ip_address');
            $table->string('ip_region', 120)->nullable()->after('ip_city');
            $table->string('ip_country', 120)->nullable()->after('ip_region');
            $table->string('ip_isp', 255)->nullable()->after('ip_country');
            $table->boolean('ip_is_proxy')->default(false)->after('ip_isp');
            $table->boolean('ip_is_hosting')->default(false)->after('ip_is_proxy');

            // URL / website validation
            $table->string('url_status', 30)->nullable()->after('ip_is_hosting');   // valid|unresolvable|parked|suspicious|redirect
            $table->boolean('url_is_https')->default(false)->after('url_status');
            $table->integer('domain_age_days')->nullable()->after('url_is_https');

            // Email classification
            $table->string('email_type', 30)->nullable()->after('domain_age_days');  // disposable|free|business

            // Spam / bot signals
            $table->boolean('honeypot_triggered')->default(false)->after('email_type');
            $table->integer('time_to_submit_seconds')->nullable()->after('honeypot_triggered');
            $table->float('recaptcha_score')->nullable()->after('time_to_submit_seconds');

            // Aggregate risk
            $table->string('spam_risk', 10)->default('low')->after('recaptcha_score'); // low|medium|high

            // Company enrichment payload
            $table->json('company_enrichment')->nullable()->after('spam_risk');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'ip_city', 'ip_region', 'ip_country', 'ip_isp', 'ip_is_proxy', 'ip_is_hosting',
                'url_status', 'url_is_https', 'domain_age_days',
                'email_type',
                'honeypot_triggered', 'time_to_submit_seconds', 'recaptcha_score',
                'spam_risk',
                'company_enrichment',
            ]);
        });
    }
};
