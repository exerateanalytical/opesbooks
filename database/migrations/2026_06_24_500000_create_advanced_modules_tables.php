<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Supplier Invoices ─────────────────────────────────────────────────
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number', 60)->unique();
            $table->string('supplier_ref', 100)->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('amount_ht', 15, 2);
            $table->decimal('tva_amount', 15, 2)->default(0);
            $table->decimal('cac_amount', 15, 2)->default(0);
            $table->decimal('amount_ttc', 15, 2);
            $table->decimal('withholding_amount', 15, 2)->default(0);
            $table->decimal('net_payable', 15, 2);
            $table->enum('status', ['DRAFT','RECEIVED','APPROVED','PAID','OVERDUE','CANCELLED'])->default('DRAFT');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_ref', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Fixed Assets ──────────────────────────────────────────────────────
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('asset_code', 30)->nullable();
            $table->enum('category', ['LAND','BUILDING','MACHINERY','VEHICLE','FURNITURE','IT_EQUIPMENT','OTHER']);
            $table->string('syscohada_account_code', 10)->default('245000');
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15, 2);
            $table->decimal('residual_value', 15, 2)->default(0);
            $table->unsignedSmallInteger('useful_life_months');
            $table->enum('depreciation_method', ['LINEAR','DECLINING'])->default('LINEAR');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->foreignId('acquisition_journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_proceeds', 15, 2)->nullable();
            $table->foreignId('disposal_journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Depreciation Entries ──────────────────────────────────────────────
        Schema::create('depreciation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('period_month');
            $table->smallInteger('period_year');
            $table->decimal('amount', 15, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['fixed_asset_id', 'period_month', 'period_year'], 'depr_asset_period_unique');
        });

        // ── Bank Reconciliation ───────────────────────────────────────────────
        Schema::create('bank_reconciliation_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('bank_account_code', 10)->default('521100');
            $table->date('statement_date');
            $table->decimal('statement_balance', 15, 2);
            $table->decimal('book_balance', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->boolean('is_reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_session_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->boolean('is_credit')->default(true);
            $table->boolean('is_matched')->default(false);
            $table->foreignId('journal_line_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // ── Budgets ───────────────────────────────────────────────────────────
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->smallInteger('fiscal_year');
            $table->enum('status', ['DRAFT','ACTIVE','CLOSED'])->default('DRAFT');
            $table->timestamps();
            $table->unique(['company_id', 'fiscal_year']);
        });

        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $table->string('account_code', 10);
            $table->tinyInteger('period_month')->nullable();
            $table->decimal('budgeted_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['budget_id', 'account_code', 'period_month']);
        });

        // ── Audit Log ─────────────────────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['company_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_reconciliation_sessions');
        Schema::dropIfExists('depreciation_entries');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('supplier_invoices');
    }
};
