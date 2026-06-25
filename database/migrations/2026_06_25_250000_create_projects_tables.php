<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('status')->default('active');     // draft, active, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget_amount', 15, 2)->nullable();
            $table->decimal('contract_value', 15, 2)->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('project_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('entry_type');                    // invoice, supplier_invoice, journal_entry, payroll_cost, expense
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->decimal('amount', 15, 2);                // + revenue, - cost
            $table->string('description');
            $table->date('entry_date');
            $table->timestamps();
            $table->index('project_id');
        });

        foreach (['journal_entries', 'customer_invoices', 'supplier_invoices', 'payroll_periods'] as $tbl) {
            if (Schema::hasTable($tbl) && ! Schema::hasColumn($tbl, 'project_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['journal_entries', 'customer_invoices', 'supplier_invoices', 'payroll_periods'] as $tbl) {
            if (Schema::hasColumn($tbl, 'project_id')) {
                Schema::table($tbl, fn (Blueprint $t) => $t->dropConstrainedForeignId('project_id'));
            }
        }
        Schema::dropIfExists('project_entries');
        Schema::dropIfExists('projects');
    }
};
