<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add SUPER_ADMIN to the users.role ENUM.
 *
 * To create a SUPER_ADMIN user after this migration:
 *   php artisan tinker
 *   \App\Models\User::create(['name'=>'Admin','email'=>'admin@opesbooks.cm','password'=>bcrypt('changeme'),'role'=>'SUPER_ADMIN']);
 */
return new class extends Migration
{
    public function up(): void
    {
        // SQLite does not support ALTER COLUMN for enums — skip if using SQLite (dev)
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN') NOT NULL DEFAULT 'OWNER'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK') NOT NULL DEFAULT 'OWNER'");
    }
};
