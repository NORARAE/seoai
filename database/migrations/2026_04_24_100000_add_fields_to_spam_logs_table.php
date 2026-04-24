<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spam_logs', function (Blueprint $table) {
            // Decision: block | flag | allow
            $table->string('action', 10)->default('block')->after('reason');

            // Submitter fields — copied at log time so they survive inquiry deletion
            $table->string('name', 255)->nullable()->after('email');
            $table->string('company', 255)->nullable()->after('name');
            $table->string('user_agent', 512)->nullable()->after('company');

            // Turnstile result
            $table->boolean('turnstile_valid')->nullable()->after('user_agent');
            // null = not checked; reason: turnstile_missing | turnstile_invalid | turnstile_error | null (passed)
            $table->string('turnstile_reason', 40)->nullable()->after('turnstile_valid');

            // Admin workflow
            $table->boolean('is_reviewed')->default(false)->after('turnstile_reason');

            $table->index('action');
            $table->index('is_reviewed');
        });
    }

    public function down(): void
    {
        Schema::table('spam_logs', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropIndex(['is_reviewed']);
            $table->dropColumn([
                'action', 'name', 'company', 'user_agent',
                'turnstile_valid', 'turnstile_reason', 'is_reviewed',
            ]);
        });
    }
};
