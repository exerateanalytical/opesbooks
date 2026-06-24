<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('dgi_validation_token')->nullable()->after('invoice_crypto_hash');
            $table->timestamp('dgi_validated_at')->nullable()->after('dgi_validation_token');
            $table->enum('dgi_sync_status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING')->after('dgi_validated_at');
            $table->text('dgi_error_payload')->nullable()->after('dgi_sync_status');
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['dgi_validation_token', 'dgi_validated_at', 'dgi_sync_status', 'dgi_error_payload']);
        });
    }
};
