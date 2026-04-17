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
            $table->timestamp('owner_notified_at')->nullable()->after('emails_sent');
        });
    }

    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropColumn('owner_notified_at');
        });
    }
};
