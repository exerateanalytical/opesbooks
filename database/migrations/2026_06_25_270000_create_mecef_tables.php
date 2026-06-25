<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mecef_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->nullable();                  // MECeF identifier from DGI
            $table->string('api_endpoint')->nullable();
            $table->text('api_token')->nullable();              // encrypted
            $table->boolean('is_active')->default(false);
            $table->boolean('sandbox_mode')->default(true);
            $table->timestamps();
            $table->unique('company_id');
        });

        Schema::create('mecef_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('customer_invoices')->nullOnDelete();
            $table->string('action');                           // certify_request, certify_success, certify_failed
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->unsignedSmallInteger('http_status')->default(0);
            $table->timestamp('created_at')->nullable();
        });

        if (Schema::hasTable('customer_invoices')) {
            Schema::table('customer_invoices', function (Blueprint $table) {
                if (! Schema::hasColumn('customer_invoices', 'mecef_status')) {
                    $table->string('mecef_status')->default('not_submitted')->after('status'); // not_submitted, pending, certified, failed, exempt
                }
                if (! Schema::hasColumn('customer_invoices', 'mecef_counter'))     $table->string('mecef_counter')->nullable();
                if (! Schema::hasColumn('customer_invoices', 'mecef_nim'))         $table->string('mecef_nim')->nullable();
                if (! Schema::hasColumn('customer_invoices', 'mecef_qr_data'))     $table->text('mecef_qr_data')->nullable();
                if (! Schema::hasColumn('customer_invoices', 'mecef_certified_at')) $table->timestamp('mecef_certified_at')->nullable();
                if (! Schema::hasColumn('customer_invoices', 'mecef_response_raw')) $table->json('mecef_response_raw')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_invoices', 'mecef_status')) {
            Schema::table('customer_invoices', function (Blueprint $table) {
                $table->dropColumn(['mecef_status', 'mecef_counter', 'mecef_nim', 'mecef_qr_data', 'mecef_certified_at', 'mecef_response_raw']);
            });
        }
        Schema::dropIfExists('mecef_logs');
        Schema::dropIfExists('mecef_configs');
    }
};
