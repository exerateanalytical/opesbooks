<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_impersonation_logs', function (Blueprint $table) {
            // WHO was impersonated (the log previously only recorded the admin).
            $table->foreignId('target_user_id')->nullable()->after('admin_user_id')
                  ->constrained('users')->nullOnDelete();
            $table->string('target_email')->nullable()->after('target_user_id');
            // The Sanctum personal-access-token id, so the session can be revoked on exit.
            $table->unsignedBigInteger('token_id')->nullable()->after('target_email');
        });
    }

    public function down(): void
    {
        Schema::table('admin_impersonation_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_impersonation_logs', 'target_user_id')) {
                $table->dropConstrainedForeignId('target_user_id');
            }
            $table->dropColumn(['target_email', 'token_id']);
        });
    }
};
