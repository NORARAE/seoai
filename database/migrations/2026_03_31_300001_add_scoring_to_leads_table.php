<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedSmallInteger('score')->default(0)->after('notes');
            $table->string('grade', 2)->nullable()->after('score');  // A, B, C, D
            $table->timestamp('scored_at')->nullable()->after('grade');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['score', 'grade', 'scored_at']);
        });
    }
};
