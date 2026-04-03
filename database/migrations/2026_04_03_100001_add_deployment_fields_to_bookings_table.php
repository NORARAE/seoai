<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('activation_date')->nullable()->after('payment_secured');
            $table->timestamp('cycle_end_date')->nullable()->after('activation_date');
            $table->string('deployment_status', 30)->nullable()->after('cycle_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['activation_date', 'cycle_end_date', 'deployment_status']);
        });
    }
};
