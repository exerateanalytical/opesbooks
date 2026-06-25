<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_configs', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2)->unique();          // CM, GA, CG, TD, GQ, CF
            $table->string('country_name_fr');
            $table->string('country_name_en');
            $table->string('flag', 8)->nullable();              // emoji
            $table->char('currency_code', 3)->default('XAF');
            $table->decimal('vat_standard_rate', 6, 2);         // e.g. 19.25
            $table->decimal('vat_reduced_rate', 6, 2)->nullable();
            $table->string('regulatory_body_name');             // e.g. "DGI Cameroun"
            $table->string('company_id_label', 10)->default('NIU'); // NIU / NIF
            $table->unsignedTinyInteger('fiscal_year_end_month')->default(12);
            $table->string('dsf_equivalent_name')->default('DSF');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->char('country_code', 2)->default('CM')->after('tax_center');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
        Schema::dropIfExists('country_configs');
    }
};
