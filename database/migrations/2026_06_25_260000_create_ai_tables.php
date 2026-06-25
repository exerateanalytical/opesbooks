<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->text('gemini_api_key')->nullable();         // encrypted cast
            $table->boolean('ollama_enabled')->default(true);
            $table->string('ollama_model')->default('gemma3:1b');
            $table->boolean('auto_categorize')->default(false);
            $table->boolean('auto_dsf_check')->default(false);
            $table->boolean('anomaly_scan')->default(false);
            $table->timestamps();
            $table->unique('company_id');
        });

        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature');                          // categorize, dsf_check, anomaly, query, receipt_parse
            $table->json('input_data')->nullable();
            $table->json('suggestion')->nullable();
            $table->string('model_used')->nullable();
            $table->boolean('was_online')->default(true);
            $table->boolean('was_accepted')->nullable();
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
        Schema::dropIfExists('ai_configs');
    }
};
