<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── customers ────────────────────────────────────────────────────────
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('niu', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->integer('payment_terms_days')->default(30);
            $table->decimal('credit_limit_xaf', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'is_active']);
        });

        // ── suppliers ────────────────────────────────────────────────────────
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('niu', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->integer('payment_terms_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'is_active']);
        });

        // ── customer_invoices ────────────────────────────────────────────────
        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number', 50)->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('amount_ht', 15, 2);
            $table->decimal('tva_amount', 15, 2)->default(0);
            $table->decimal('cac_amount', 15, 2)->default(0);
            $table->decimal('amount_ttc', 15, 2);
            $table->enum('status', ['DRAFT', 'SENT', 'PAID', 'OVERDUE', 'CANCELLED', 'CREDIT_NOTE'])->default('DRAFT');
            $table->foreignId('credit_note_for_id')->nullable()->references('id')->on('customer_invoices')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_date']);
        });

        // ── recurring_transactions ────────────────────────────────────────────
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('debit_account', 10);
            $table->string('credit_account', 10);
            $table->decimal('amount_xaf', 15, 2);
            $table->text('memo');
            $table->enum('frequency', ['DAILY', 'WEEKLY', 'MONTHLY', 'QUARTERLY', 'YEARLY']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'is_active', 'next_run_date']);
        });

        // ── journal_entry_attachments ─────────────────────────────────────────
        Schema::create('journal_entry_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size_bytes');
            $table->timestamps();
            $table->index('journal_entry_id');
        });

        // ── employees ─────────────────────────────────────────────────────────
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('cnps_number', 30)->nullable();
            $table->string('position')->nullable();
            $table->decimal('gross_salary_xaf', 15, 2);
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'is_active']);
        });

        // ── payroll_periods ───────────────────────────────────────────────────
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->enum('status', ['DRAFT', 'POSTED'])->default('DRAFT');
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_cnps_employee', 15, 2)->default(0);
            $table->decimal('total_cnps_employer', 15, 2)->default(0);
            $table->decimal('total_irpp', 15, 2)->default(0);
            $table->decimal('total_cac_irpp', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->unique(['company_id', 'period_month', 'period_year']);
        });

        // ── payroll_lines ─────────────────────────────────────────────────────
        Schema::create('payroll_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('cnps_employee', 15, 2)->default(0);
            $table->decimal('cnps_employer', 15, 2)->default(0);
            $table->decimal('irpp', 15, 2)->default(0);
            $table->decimal('cac_irpp', 15, 2)->default(0);
            $table->decimal('rav', 15, 2)->default(625); // 7500/12 XAF/month
            $table->decimal('net_salary', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_lines');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('journal_entry_attachments');
        Schema::dropIfExists('recurring_transactions');
        Schema::dropIfExists('customer_invoices');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};
