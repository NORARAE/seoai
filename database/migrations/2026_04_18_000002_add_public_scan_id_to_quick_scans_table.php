<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->string('public_scan_id', 32)->nullable()->after('id');
        });

        DB::table('quick_scans')
            ->select(['id'])
            ->whereNull('public_scan_id')
            ->orderBy('id')
            ->chunkById(500, function ($scans): void {
                foreach ($scans as $scan) {
                    DB::table('quick_scans')
                        ->where('id', $scan->id)
                        ->update([
                            'public_scan_id' => 'SCAN-' . str_pad((string) $scan->id, 5, '0', STR_PAD_LEFT),
                        ]);
                }
            });

        Schema::table('quick_scans', function (Blueprint $table) {
            $table->unique('public_scan_id');
        });
    }

    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropUnique(['public_scan_id']);
            $table->dropColumn('public_scan_id');
        });
    }
};
