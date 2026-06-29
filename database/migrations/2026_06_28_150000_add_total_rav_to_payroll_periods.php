<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            // RAV (Redevance Audiovisuelle) was deducted from net pay but never
            // accumulated or credited in the journal, leaving every payroll entry
            // unbalanced and impossible to post.
            $table->decimal('total_rav', 15, 2)->default(0)->after('total_tsr');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn('total_rav');
        });
    }
};
