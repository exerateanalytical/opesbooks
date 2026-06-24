<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('niu', 20)->unique();
            $table->string('rccm', 50)->unique();
            $table->enum('tax_regime', ['REEL', 'SIMPLIFIE', 'LIBERATOIRE']);
            $table->string('tax_center');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('letterhead_tagline', 255)->nullable();
            $table->string('letterhead_website', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('bank_rib', 100)->nullable();
            $table->string('invoice_footer_note', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('syscohada_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('label');
            $table->tinyInteger('class_digit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('posting_date');
            $table->string('reference_id', 100)->unique();
            $table->enum('source_pipeline', [
                'AUTOMATED_MOMO',
                'AUTOMATED_ORANGE',
                'MANUAL_CASH',
                'MANUAL_BANK',
                'MANUAL_INVOICE',
            ]);
            $table->text('memo');
            $table->enum('status', ['DRAFT', 'POSTED', 'REVERSED'])->default('POSTED');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'posting_date']);
            $table->index('source_pipeline');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('syscohada_account_id')->constrained('syscohada_accounts');
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('syscohada_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('syscohada_accounts');
        Schema::dropIfExists('companies');
    }
};
