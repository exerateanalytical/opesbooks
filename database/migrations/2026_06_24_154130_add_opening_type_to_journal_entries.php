<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: extend enum to add OPENING and REVERSAL types.
        // The posting_type column is actually created later (in the enterprise
        // schema migration) with the full enum, so this only applies when the
        // column already exists — otherwise it's a safe no-op. (Prevents a
        // "Unknown column posting_type" crash on fresh MySQL deploys.)
        // SQLite: TEXT column — no ALTER needed; values enforced at app level.
        if (DB::getDriverName() === 'mysql' && Schema::hasColumn('journal_entries', 'posting_type')) {
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
