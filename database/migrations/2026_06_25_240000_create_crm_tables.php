<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('contact_name');
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('company_name')->nullable();
            $table->string('source')->default('other');   // referral, cold_call, walk_in, social_media, other
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->string('status')->default('new');      // new, contacted, qualified, proposal_sent, won, lost
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lost_reason')->nullable();
            $table->timestamp('stage_changed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('note');       // call, meeting, email, whatsapp, note
            $table->text('description');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index('lead_id');
        });

        if (Schema::hasTable('customer_quotations') && ! Schema::hasColumn('customer_quotations', 'lead_id')) {
            Schema::table('customer_quotations', function (Blueprint $table) {
                $table->foreignId('lead_id')->nullable()->after('company_id')->constrained('crm_leads')->nullOnDelete();
            });
        }
        if (Schema::hasTable('customer_invoices') && ! Schema::hasColumn('customer_invoices', 'lead_id')) {
            Schema::table('customer_invoices', function (Blueprint $table) {
                $table->foreignId('lead_id')->nullable()->after('company_id')->constrained('crm_leads')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_quotations', 'lead_id')) {
            Schema::table('customer_quotations', fn (Blueprint $t) => $t->dropConstrainedForeignId('lead_id'));
        }
        if (Schema::hasColumn('customer_invoices', 'lead_id')) {
            Schema::table('customer_invoices', fn (Blueprint $t) => $t->dropConstrainedForeignId('lead_id'));
        }
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_leads');
    }
};
