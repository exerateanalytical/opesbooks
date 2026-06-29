<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syscohada_accounts', function (Blueprint $table) {
            // NULL = standard/shared SYSCOHADA account (read-only for tenants);
            // set = a tenant's own custom account (only they may edit it).
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('syscohada_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
