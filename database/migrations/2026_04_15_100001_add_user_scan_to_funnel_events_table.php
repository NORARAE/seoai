<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('funnel_events', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('session_token')->constrained('users')->nullOnDelete();
            $table->foreignId('scan_id')->nullable()->after('user_id')->constrained('quick_scans')->nullOnDelete();
            $table->index(['user_id', 'event_name']);
            $table->index(['created_at', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::table('funnel_events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['scan_id']);
            $table->dropIndex(['user_id', 'event_name']);
            $table->dropIndex(['created_at', 'event_name']);
            $table->dropColumn(['user_id', 'scan_id']);
        });
    }
};
