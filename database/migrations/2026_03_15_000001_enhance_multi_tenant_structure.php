<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enhances existing clients table to full tenant structure
     * and adds tenant relationships to users table
     */
    public function up(): void
    {
        // Enhance clients table to be full tenant table
        Schema::table('clients', function (Blueprint $table) {
            $table->string('subdomain')->unique()->nullable()->after('company_name');
            $table->string('domain')->nullable()->after('subdomain');
            $table->json('settings')->nullable()->after('phone');
            $table->timestamp('trial_ends_at')->nullable()->after('status');
            $table->timestamp('suspended_at')->nullable()->after('trial_ends_at');
            $table->string('timezone')->default('UTC')->after('suspended_at');
            $table->unsignedInteger('max_sites')->default(1)->after('timezone');
            $table->unsignedInteger('max_pages')->default(100)->after('max_sites');
            $table->unsignedInteger('max_ai_operations_per_month')->default(50)->after('max_pages');
        });

        // Add tenant relationship to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->string('role')->default('member')->after('email'); // owner, admin, member
            $table->json('permissions')->nullable()->after('role');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            
            $table->index(['client_id', 'role']);
        });

        // Create roles table for more granular RBAC
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade'); // Null = platform-wide role
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['client_id', 'slug']);
        });

        // Create role_user pivot table
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id',
                'role',
                'permissions',
                'last_login_at',
                'is_active'
            ]);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'subdomain',
                'domain',
                'settings',
                'trial_ends_at',
                'suspended_at',
                'timezone',
                'max_sites',
                'max_pages',
                'max_ai_operations_per_month'
            ]);
        });
    }
};
