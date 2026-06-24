<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo_path', 500)->nullable()->after('address');
            $table->string('letterhead_tagline', 255)->nullable()->after('logo_path');
            $table->string('letterhead_website', 255)->nullable()->after('letterhead_tagline');
            $table->string('bank_name', 255)->nullable()->after('letterhead_website');
            $table->string('bank_account', 100)->nullable()->after('bank_name');
            $table->string('bank_rib', 100)->nullable()->after('bank_account');
            $table->string('invoice_footer_note', 500)->nullable()->after('bank_rib');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'letterhead_tagline', 'letterhead_website',
                'bank_name', 'bank_account', 'bank_rib', 'invoice_footer_note',
            ]);
        });
    }
};
