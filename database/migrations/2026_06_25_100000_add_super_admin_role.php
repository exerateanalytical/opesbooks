<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add SUPER_ADMIN to the users.role ENUM/CHECK.
 *
 * To create a SUPER_ADMIN user after this migration:
 *   php artisan tinker
 *   \App\Models\User::create(['name'=>'Admin','email'=>'admin@opesbooks.cm','password'=>bcrypt('changeme'),'role'=>'SUPER_ADMIN']);
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: rebuild the table without the old CHECK constraint
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    email_verified_at DATETIME,
                    password TEXT NOT NULL,
                    role TEXT NOT NULL DEFAULT \'OWNER\'
                        CHECK(role IN (\'OWNER\',\'ACCOUNTANT\',\'CLERK\',\'SUPER_ADMIN\')),
                    company_id INTEGER,
                    assigned_caisse_code TEXT,
                    otp_code TEXT,
                    otp_expires_at DATETIME,
                    two_fa_enabled INTEGER NOT NULL DEFAULT 0,
                    remember_token TEXT,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ');
            DB::statement('INSERT INTO users_new SELECT * FROM users');
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON');
            return;
        }

        // MySQL / MariaDB
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN') NOT NULL DEFAULT 'OWNER'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    email_verified_at DATETIME,
                    password TEXT NOT NULL,
                    role TEXT NOT NULL DEFAULT \'OWNER\'
                        CHECK(role IN (\'OWNER\',\'ACCOUNTANT\',\'CLERK\')),
                    company_id INTEGER,
                    assigned_caisse_code TEXT,
                    otp_code TEXT,
                    otp_expires_at DATETIME,
                    two_fa_enabled INTEGER NOT NULL DEFAULT 0,
                    remember_token TEXT,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ');
            DB::statement('INSERT INTO users_new SELECT id, name, email, email_verified_at, password, CASE WHEN role = \'SUPER_ADMIN\' THEN \'OWNER\' ELSE role END, company_id, assigned_caisse_code, otp_code, otp_expires_at, two_fa_enabled, remember_token, created_at, updated_at FROM users');
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON');
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK') NOT NULL DEFAULT 'OWNER'");
    }
};
