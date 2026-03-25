<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function applyStatusEnum(array $values, string $default = 'draft'): void
    {
        Schema::table('page_payloads', function (Blueprint $table) use ($values, $default): void {
            $table->enum('status', $values)
                ->default($default)
                ->change();
        });
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TABLE IF EXISTS __temp__page_payloads');
        }

        Schema::table('page_payloads', function (Blueprint $table): void {
            if (! Schema::hasColumn('page_payloads', 'reviewed_by_user_id')) {
                $table->foreignId('reviewed_by_user_id')->nullable()->after('publish_notes')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('page_payloads', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
            }

            if (! Schema::hasColumn('page_payloads', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('reviewed_at');
            }
        });

        // SQLite rebuilds table constraints during enum changes; use a transitional
        // superset so legacy rows ('ready', 'archived') copy safely before backfill.
        $this->applyStatusEnum([
            'draft',
            'ready',
            'needs_review',
            'approved',
            'rejected',
            'published',
            'archived',
            'failed',
        ]);

        DB::table('page_payloads')
            ->where('status', 'ready')
            ->update(['status' => 'needs_review']);

        DB::table('page_payloads')
            ->where('status', 'archived')
            ->update(['status' => 'draft']);

        DB::table('page_payloads')
            ->where('publish_status', 'failed')
            ->whereNotIn('status', ['published'])
            ->update(['status' => 'failed']);

        // Enforce the final editorial workflow enum after data is normalized.
        $this->applyStatusEnum([
            'draft',
            'needs_review',
            'approved',
            'rejected',
            'published',
            'failed',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TABLE IF EXISTS __temp__page_payloads');
        }

        // Transitional superset for safe reverse-mapping.
        $this->applyStatusEnum([
            'draft',
            'ready',
            'needs_review',
            'approved',
            'rejected',
            'published',
            'archived',
            'failed',
        ]);

        DB::table('page_payloads')
            ->where('status', 'needs_review')
            ->update(['status' => 'ready']);

        DB::table('page_payloads')
            ->where('status', 'rejected')
            ->update(['status' => 'draft']);

        DB::table('page_payloads')
            ->where('status', 'failed')
            ->where('publish_status', 'failed')
            ->update(['status' => 'draft']);

        $this->applyStatusEnum(['draft', 'ready', 'published', 'archived']);

        Schema::table('page_payloads', function (Blueprint $table): void {
            if (Schema::hasColumn('page_payloads', 'reviewed_by_user_id')) {
                $table->dropConstrainedForeignId('reviewed_by_user_id');
            }

            $dropColumns = array_values(array_filter([
                Schema::hasColumn('page_payloads', 'reviewed_at') ? 'reviewed_at' : null,
                Schema::hasColumn('page_payloads', 'review_notes') ? 'review_notes' : null,
            ]));

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};