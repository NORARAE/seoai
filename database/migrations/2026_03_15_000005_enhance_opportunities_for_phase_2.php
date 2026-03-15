<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enhances opportunities table for advanced SEO opportunity detection
     */
    public function up(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Rename issue_type to type for consistency
            $table->renameColumn('issue_type', 'type');
            
            // Add polymorphic page relation (supports Page and LocationPage)
            $table->dropForeign(['page_id']);
            $table->renameColumn('page_id', 'page_id_old');
            
            $table->after('site_id', function (Blueprint $table) {
                $table->morphs('opportunifiable'); // opportunifiable_type, opportunifiable_id
            });
            
            // Enhanced scoring and metrics
            $table->decimal('score', 5, 2)->default(0)->after('priority_score'); // 0-100 normalized score
            $table->json('metrics')->nullable()->after('score'); // Detailed metrics
            
            // Enhanced description
            $table->text('description')->nullable()->after('recommendation');
            
            // Action tracking
            $table->foreignId('addressed_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('addressed_at')->nullable()->after('addressed_by');
            $table->text('resolution_notes')->nullable()->after('addressed_at');
            
            // Link to optimization run if action was taken
            $table->foreignId('optimization_run_id')->nullable()->after('resolution_notes')
                ->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['addressed_by']);
            $table->dropForeign(['optimization_run_id']);
            
            $table->dropColumn([
                'opportunifiable_type',
                'opportunifiable_id',
                'score',
                'metrics',
                'description',
                'addressed_by',
                'addressed_at',
                'resolution_notes',
                'optimization_run_id'
            ]);
            
            $table->renameColumn('page_id_old', 'page_id');
            $table->renameColumn('type', 'issue_type');
            
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }
};
