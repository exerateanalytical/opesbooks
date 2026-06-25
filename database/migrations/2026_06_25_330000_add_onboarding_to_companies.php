<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'onboarding_completed'))       $table->boolean('onboarding_completed')->default(false);
            if (! Schema::hasColumn('companies', 'onboarding_step'))            $table->unsignedTinyInteger('onboarding_step')->default(1);
            if (! Schema::hasColumn('companies', 'onboarding_completed_at'))    $table->timestamp('onboarding_completed_at')->nullable();
            if (! Schema::hasColumn('companies', 'onboarding_checklist_dismissed')) $table->boolean('onboarding_checklist_dismissed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed', 'onboarding_step', 'onboarding_completed_at', 'onboarding_checklist_dismissed']);
        });
    }
};
