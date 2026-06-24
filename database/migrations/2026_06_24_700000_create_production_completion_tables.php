<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Customer credit notes (Avoirs clients) ─────────────────────────
        Schema::create('customer_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('original_invoice_id')->nullable()->constrained('customer_invoices')->nullOnDelete();
            $table->string('credit_note_number', 50)->unique();
            $table->date('credit_note_date');
            $table->text('reason')->nullable();
            $table->decimal('amount_ht', 14, 2)->default(0);
            $table->decimal('tva_amount', 14, 2)->default(0);
            $table->decimal('cac_amount', 14, 2)->default(0);
            $table->decimal('amount_ttc', 14, 2)->default(0);
            $table->enum('status', ['DRAFT', 'ISSUED', 'APPLIED'])->default('DRAFT');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'customer_id']);
        });

        // ── Supplier credit notes (Avoirs fournisseurs) ────────────────────
        Schema::create('supplier_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('original_invoice_id')->nullable()->constrained('supplier_invoices')->nullOnDelete();
            $table->string('credit_note_number', 50)->unique();
            $table->date('credit_note_date');
            $table->text('reason')->nullable();
            $table->decimal('amount_ht', 14, 2)->default(0);
            $table->decimal('tva_amount', 14, 2)->default(0);
            $table->decimal('net_payable', 14, 2)->default(0);
            $table->enum('status', ['DRAFT', 'RECEIVED', 'APPLIED'])->default('DRAFT');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'supplier_id']);
        });

        // ── Purchase orders (Bons de commande fournisseurs) ────────────────
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('po_number', 50)->unique();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('amount_ht', 14, 2)->default(0);
            $table->decimal('tva_amount', 14, 2)->default(0);
            $table->decimal('amount_ttc', 14, 2)->default(0);
            $table->enum('status', ['DRAFT', 'SENT', 'PARTIAL', 'RECEIVED', 'CANCELLED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'supplier_id']);
        });

        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('description', 255);
            $table->string('account_code', 10)->nullable();
            $table->decimal('quantity', 14, 4)->default(1);
            $table->decimal('unit_price_ht', 14, 2)->default(0);
            $table->decimal('line_total_ht', 14, 2)->default(0);
            $table->decimal('qty_received', 14, 4)->default(0);
            $table->timestamps();
        });

        // ── Customer quotations (Devis clients / Pro-forma) ────────────────
        Schema::create('customer_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('converted_invoice_id')->nullable()->constrained('customer_invoices')->nullOnDelete();
            $table->string('quotation_number', 50)->unique();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->decimal('amount_ht', 14, 2)->default(0);
            $table->decimal('tva_amount', 14, 2)->default(0);
            $table->decimal('cac_amount', 14, 2)->default(0);
            $table->decimal('amount_ttc', 14, 2)->default(0);
            $table->enum('status', ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'CONVERTED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'customer_id']);
        });

        Schema::create('customer_quotation_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_quotation_id')->constrained()->cascadeOnDelete();
            $table->string('description', 255);
            $table->string('account_code', 10)->nullable();
            $table->decimal('quantity', 14, 4)->default(1);
            $table->decimal('unit_price_ht', 14, 2)->default(0);
            $table->decimal('line_total_ht', 14, 2)->default(0);
            $table->timestamps();
        });

        // ── Patente (local business tax) tracking ──────────────────────────
        Schema::create('patente_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->year('tax_year');
            $table->string('patente_number', 100)->nullable();
            $table->decimal('amount_due_xaf', 14, 2)->default(0);
            $table->decimal('amount_paid_xaf', 14, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'OVERDUE'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'tax_year']);
        });

        // ── Password reset tokens ──────────────────────────────────────────
        // Note: Laravel's built-in password_reset_tokens table is already
        // created by the default auth migration (0001_01_01_000000).
        // We just ensure the table exists via the standard migration.
    }

    public function down(): void
    {
        Schema::dropIfExists('patente_records');
        Schema::dropIfExists('customer_quotation_lines');
        Schema::dropIfExists('customer_quotations');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('supplier_credit_notes');
        Schema::dropIfExists('customer_credit_notes');
    }
};
