<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('product_code', 50);
            $table->string('product_name', 255);
            $table->string('account_code', 10);        // SYSCOHADA Class 3 account (31xxxx, 32xxxx…)
            $table->enum('movement_type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost_xaf', 14, 2);   // weighted-average unit cost at time of movement
            $table->decimal('total_cost_xaf', 14, 2);  // quantity × unit_cost_xaf
            $table->date('movement_date');
            $table->string('reference', 100)->nullable(); // invoice number, PO ref, etc.
            $table->text('description')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'product_code', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
