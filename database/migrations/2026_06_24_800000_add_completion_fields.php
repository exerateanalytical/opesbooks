<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TSR on payroll_lines (Taxe sur Salaires Retraites — 1% employer on gross)
        Schema::table('payroll_lines', function (Blueprint $table) {
            $table->decimal('tsr_employer', 15, 2)->default(0)->after('cac_irpp');
        });

        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->decimal('total_tsr', 15, 2)->default(0)->after('total_cac_irpp');
        });

        // Customer-side withholding (when buyer is DGE/CIME and withholds from us)
        Schema::table('customer_invoices', function (Blueprint $table) {
            $table->decimal('withholding_received', 15, 2)->default(0)->after('amount_ttc');
            $table->decimal('net_receivable', 15, 2)->nullable()->after('withholding_received');
        });

        // Email OTP for 2FA
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code', 6)->nullable()->after('remember_token');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('two_fa_enabled')->default(false)->after('otp_expires_at');
        });

        // Delivery notes (Bons de livraison)
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('dn_type', 10)->default('OUT'); // OUT=sale delivery, IN=purchase receipt
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_invoice_id')->nullable()->constrained('customer_invoices')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('dn_number', 50)->unique();
            $table->date('delivery_date');
            $table->string('delivery_address', 500)->nullable();
            $table->enum('status', ['DRAFT', 'DELIVERED', 'SIGNED'])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'dn_type', 'delivery_date']);
        });

        Schema::create('delivery_note_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->string('description', 255);
            $table->string('product_code', 50)->nullable();
            $table->decimal('quantity', 14, 4)->default(1);
            $table->string('unit', 30)->nullable(); // kg, pcs, carton…
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_lines');
        Schema::dropIfExists('delivery_notes');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'two_fa_enabled']);
        });

        Schema::table('customer_invoices', function (Blueprint $table) {
            $table->dropColumn(['withholding_received', 'net_receivable']);
        });

        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn('total_tsr');
        });

        Schema::table('payroll_lines', function (Blueprint $table) {
            $table->dropColumn('tsr_employer');
        });
    }
};
