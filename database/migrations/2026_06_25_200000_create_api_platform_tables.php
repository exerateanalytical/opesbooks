<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── API keys (API-as-a-product) ──────────────────────────────────
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('environment', 10)->default('live');   // live | test
            $table->string('key_prefix', 24)->index();            // ob_live_sk_xxxx (display)
            $table->string('key_hash', 64)->unique();             // sha256 of full key
            $table->json('scopes')->nullable();                   // ["invoices:read", ...]
            $table->unsignedInteger('rate_limit')->default(1000); // requests / hour
            $table->string('status', 12)->default('ACTIVE');      // ACTIVE | REVOKED
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ── API request logs (observability) ─────────────────────────────
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 8);
            $table->string('endpoint');
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('latency_ms')->default(0);
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['company_id', 'created_at']);
            $table->index(['status_code']);
        });

        // ── Platform announcements ───────────────────────────────────────
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('type', 16)->default('INFO');          // INFO | WARNING | MAINTENANCE | FEATURE
            $table->string('target_plan', 16)->nullable();        // null = all plans
            $table->foreignId('target_company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('announcements');
    }
};
