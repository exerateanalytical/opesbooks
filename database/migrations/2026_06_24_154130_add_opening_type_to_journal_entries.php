<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: extend enum to add OPENING and REVERSAL types
        // SQLite: TEXT column — no ALTER needed; values are enforced at app level only
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE journal_entries MODIFY posting_type ENUM('STANDARD','ADJUSTMENT','OPENING','REVERSAL') NOT NULL DEFAULT 'STANDARD'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE journal_entries MODIFY posting_type ENUM('STANDARD','ADJUSTMENT') NOT NULL DEFAULT 'STANDARD'");
        }
    }
};
