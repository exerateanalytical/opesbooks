<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'two_factor_secret'))         $table->text('two_factor_secret')->nullable();
            if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) $table->text('two_factor_recovery_codes')->nullable();
            if (! Schema::hasColumn('users', 'two_factor_confirmed_at'))   $table->timestamp('two_factor_confirmed_at')->nullable();
            if (! Schema::hasColumn('users', 'last_login_at'))             $table->timestamp('last_login_at')->nullable();
            if (! Schema::hasColumn('users', 'last_login_ip'))             $table->string('last_login_ip', 45)->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'require_2fa')) $table->boolean('require_2fa')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'last_login_at', 'last_login_ip']);
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('require_2fa');
        });
    }
};
