<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price_xaf_monthly')->default(0);
            $table->unsignedBigInteger('price_xaf_yearly')->default(0);
            $table->integer('max_users')->default(1);                 // -1 = unlimited
            $table->integer('max_invoices_per_month')->default(20);   // -1 = unlimited
            $table->integer('api_calls_per_hour')->default(0);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // trial_started, plan_upgraded, plan_downgraded, payment_received, payment_failed, suspended, reactivated, cancelled
            $table->string('from_plan')->nullable();
            $table->string('to_plan')->nullable();
            $table->unsignedBigInteger('amount_xaf')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('plan_slug')->nullable();
            $table->unsignedBigInteger('amount_xaf');
            $table->char('currency', 3)->default('XAF');
            $table->string('payment_method'); // orange_money, mtn_momo, bank_transfer, cash, manual, stripe
            $table->string('reference')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('status')->default('completed'); // pending, completed, failed, refunded
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'plan_slug'))               $table->string('plan_slug')->default('free')->after('country_code');
            if (! Schema::hasColumn('companies', 'trial_ends_at'))           $table->timestamp('trial_ends_at')->nullable();
            if (! Schema::hasColumn('companies', 'subscription_started_at')) $table->timestamp('subscription_started_at')->nullable();
            if (! Schema::hasColumn('companies', 'subscription_renewed_at')) $table->timestamp('subscription_renewed_at')->nullable();
            if (! Schema::hasColumn('companies', 'next_billing_at'))         $table->timestamp('next_billing_at')->nullable();
            if (! Schema::hasColumn('companies', 'custom_price_xaf'))        $table->unsignedBigInteger('custom_price_xaf')->nullable();
        });
    }

    public function down(): void
    {
        foreach (['plan_slug', 'trial_ends_at', 'subscription_started_at', 'subscription_renewed_at', 'next_billing_at', 'custom_price_xaf'] as $col) {
            if (Schema::hasColumn('companies', $col)) {
                Schema::table('companies', fn (Blueprint $t) => $t->dropColumn($col));
            }
        }
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscription_events');
        Schema::dropIfExists('plan_configs');
    }
};
