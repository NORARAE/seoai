<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * SaaS billing and subscription management
     */
    public function up(): void
    {
        // Plans table - defines available subscription tiers
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Starter, Professional, Enterprise
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            
            // Limits
            $table->unsignedInteger('max_sites')->default(1);
            $table->unsignedInteger('max_pages')->default(100);
            $table->unsignedInteger('max_ai_operations_per_month')->default(50);
            $table->unsignedInteger('max_users')->default(1);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_white_label')->default(false);
            $table->boolean('has_priority_support')->default(false);
            
            // Features
            $table->json('features')->nullable(); // Flexible feature flags
            
            // Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true); // Show on pricing page
            $table->unsignedInteger('sort_order')->default(0);
            
            // External IDs
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
            
            $table->timestamps();
            
            $table->index(['is_active', 'is_public']);
        });

        // Subscriptions table - tracks client subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            
            // Status: trial, active, past_due, canceled, expired
            $table->string('status')->default('trial');
            
            // Billing cycle
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            
            // Dates
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            
            // Payment provider
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->json('payment_metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'status']);
            $table->index(['status', 'next_billing_date']);
            $table->index('stripe_subscription_id');
        });

        // Usage tracking table - tracks resource consumption
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            
            // Resource type
            $table->string('resource_type'); // page_generation, ai_operation, api_call
            $table->unsignedInteger('quantity')->default(1);
            
            // Tracking period
            $table->date('period_start');
            $table->date('period_end');
            
            // Context
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'resource_type', 'period_start']);
            $table->index(['subscription_id', 'period_start']);
        });

        // Invoices table - billing history
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('invoice_number')->unique();
            
            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency')->default('USD');
            
            // Status: draft, pending, paid, failed, refunded
            $table->string('status')->default('pending');
            
            // Dates
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Payment
            $table->string('stripe_invoice_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->json('line_items')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['client_id', 'status']);
            $table->index('invoice_date');
            $table->index('stripe_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('usage_records');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
