<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds:
 *   - users.home_company_id       — preserves original company when a firm accountant opens a client
 *   - firm_clients.locked_until   — period locking per client (fix #16)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('home_company_id')->nullable()->after('company_id');
        });

        Schema::table('firm_clients', function (Blueprint $table) {
            $table->date('locked_until')->nullable()->after('onboarded_at');
        });
    }

    public function down(): void
    {
        Schema::table('firm_clients', function (Blueprint $table) {
            $table->dropColumn('locked_until');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('home_company_id');
        });
    }
};
