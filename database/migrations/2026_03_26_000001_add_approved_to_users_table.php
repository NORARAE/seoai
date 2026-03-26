<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('is_active')->index();
        });

        // Backfill: existing privileged users keep access — safe strategy using raw role column,
        // no model/scope dependency, so it works on any DB state.
        DB::table('users')
            ->whereIn('role', ['super_admin', 'superadmin', 'admin', 'owner', 'account_manager'])
            ->update(['approved' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['approved']);
            $table->dropColumn('approved');
        });
    }
};
