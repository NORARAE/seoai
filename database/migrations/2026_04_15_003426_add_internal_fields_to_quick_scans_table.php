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
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->boolean('is_internal')->default(false)->after('emails_sent');
            $table->string('source', 50)->nullable()->after('is_internal');
            $table->boolean('suppress_emails')->default(false)->after('source');
            $table->foreignId('initiated_by')->nullable()->after('suppress_emails')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropForeign(['initiated_by']);
            $table->dropColumn(['is_internal', 'source', 'suppress_emails', 'initiated_by']);
        });
    }
};
