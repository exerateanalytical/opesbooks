<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add the read-only AUDITOR role to users.role.
 * SQLite (dev): patch the CHECK list in place (no table rebuild → drift-proof).
 * MySQL (prod): MODIFY the ENUM.
 */
return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'")[0]->sql;
            if (! str_contains($sql, "'AUDITOR'")) {
                $new = str_replace("'FIRM_ACCOUNTANT'))", "'FIRM_ACCOUNTANT','AUDITOR'))", $sql);
                if ($new === $sql) { // fallback if the list ends differently
                    $new = preg_replace("/(role IN \([^)]*)\)\)/", "\$1,'AUDITOR'))", $sql, 1);
                }
                DB::statement('PRAGMA writable_schema = ON');
                DB::update("UPDATE sqlite_master SET sql = ? WHERE type='table' AND name='users'", [$new]);
                DB::statement('PRAGMA writable_schema = OFF');
            }
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN','FIRM_ACCOUNTANT','AUDITOR') NOT NULL DEFAULT 'OWNER'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN','FIRM_ACCOUNTANT') NOT NULL DEFAULT 'OWNER'");
        }
    }
};
