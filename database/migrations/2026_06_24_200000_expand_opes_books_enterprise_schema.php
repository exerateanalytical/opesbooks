<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Expand companies ─────────────────────────────────────────────────
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('vat_prorata_coefficient', 5, 2)->default(100.00)->after('tax_center');
            $table->enum('subscription_status', ['ACTIVE', 'PAST_DUE', 'SUSPENDED'])->default('ACTIVE')->after('vat_prorata_coefficient');
        });

        // ── Replace scaffold users table with multi-tenant version ──────────
        // The default Laravel users table stays but we add company binding + role
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->enum('role', ['OWNER', 'ACCOUNTANT', 'CLERK'])->default('OWNER')->after('email');
            $table->string('assigned_caisse_code', 10)->nullable()->after('role');
        });

        // ── Expand journal_entries ───────────────────────────────────────────
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->enum('posting_type', ['STANDARD', 'ADJUSTMENT', 'OPENING', 'REVERSAL'])->default('STANDARD')->after('memo');
            $table->string('invoice_crypto_hash', 64)->nullable()->after('posting_type');
            $table->enum('transaction_status', ['PENDING', 'SUCCESSFUL', 'FAILED', 'REVERSED'])->default('SUCCESSFUL')->after('invoice_crypto_hash');
        });

        // Remove old status column — replaced by transaction_status
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // ── currency_conversions ─────────────────────────────────────────────
        Schema::create('currency_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('foreign_currency', 3);
            $table->decimal('exchange_rate_to_xaf', 10, 4);
            $table->date('rate_date');
            $table->timestamps();

            $table->unique(['foreign_currency', 'rate_date']);
        });

        // ── raw_payload_queue (async MoMo ingestion buffer) ──────────────────
        Schema::create('raw_payload_queue', function (Blueprint $table) {
            $table->id();
            $table->string('operator', 10);
            $table->string('transaction_id', 100)->unique();
            $table->string('company_niu', 30);
            $table->decimal('amount', 15, 2);
            $table->string('message', 500);
            $table->date('txn_date');
            $table->enum('status', ['QUEUED', 'PROCESSING', 'DONE', 'FAILED'])->default('QUEUED');
            $table->text('error_detail')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // ── subscriptions ────────────────────────────────────────────────────
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['STARTER', 'GROWTH', 'ENTERPRISE'])->default('STARTER');
            $table->decimal('amount_xaf', 10, 2);
            $table->string('billing_phone', 20);
            $table->enum('status', ['ACTIVE', 'PENDING', 'PAST_DUE', 'CANCELLED'])->default('PENDING');
            $table->string('aggregator_reference')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('raw_payload_queue');
        Schema::dropIfExists('currency_conversions');

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'posting_type', 'invoice_crypto_hash', 'transaction_status']);
            $table->enum('status', ['DRAFT', 'POSTED', 'REVERSED'])->default('POSTED');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'role', 'assigned_caisse_code']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['vat_prorata_coefficient', 'subscription_status']);
        });
    }
};
