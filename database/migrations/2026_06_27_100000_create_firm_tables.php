<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Enterprise / Cabinet Comptable layer.
 *
 * Adds:
 *   - firms              (the accounting firm entity)
 *   - firm_clients       (companies managed by a firm)
 *   - firm_users         (staff who belong to a firm)
 *   - FIRM_ACCOUNTANT to users.role CHECK constraint
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── firms ────────────────────────────────────────────────────────────
        Schema::create('firms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('oecam_number')->nullable();   // Ordre des Experts-Comptables du Cameroun
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable()->default('Douala');
            $table->string('logo_path')->nullable();
            $table->integer('max_clients')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── firm_clients (companies managed by this firm) ─────────────────
        Schema::create('firm_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_accountant_id')
                  ->nullable()->constrained('users')->nullOnDelete();
            $table->string('engagement_type')->default('FULL_OUTSOURCE');
            // FULL_OUTSOURCE | REVIEW_ONLY | TAX_ONLY | PAYROLL_ONLY
            $table->string('billing_mode')->default('FIRM_PAYS');
            // FIRM_PAYS | CLIENT_PAYS | HYBRID
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamps();
            $table->unique(['firm_id', 'company_id']);
        });

        // ── firm_users (staff who belong to the firm) ─────────────────────
        Schema::create('firm_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('firm_role')->default('JUNIOR');
            // PARTNER | SENIOR | JUNIOR | ASSISTANT
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['firm_id', 'user_id']);
        });

        // ── Add FIRM_ACCOUNTANT to users.role ─────────────────────────────
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    email_verified_at DATETIME,
                    password TEXT NOT NULL,
                    role TEXT NOT NULL DEFAULT 'OWNER'
                        CHECK(role IN ('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN','FIRM_ACCOUNTANT')),
                    company_id INTEGER,
                    assigned_caisse_code TEXT,
                    otp_code TEXT,
                    otp_expires_at DATETIME,
                    two_fa_enabled INTEGER NOT NULL DEFAULT 0,
                    remember_token TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    two_factor_secret TEXT,
                    two_factor_recovery_codes TEXT,
                    two_factor_confirmed_at DATETIME,
                    last_login_at DATETIME,
                    last_login_ip VARCHAR(45)
                )
            ");
            DB::statement('INSERT INTO users_new SELECT id, name, email, email_verified_at, password, role, company_id, assigned_caisse_code, otp_code, otp_expires_at, two_fa_enabled, remember_token, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, last_login_at, last_login_ip FROM users');
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN','FIRM_ACCOUNTANT') NOT NULL DEFAULT 'OWNER'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('firm_users');
        Schema::dropIfExists('firm_clients');
        Schema::dropIfExists('firms');

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    email_verified_at DATETIME,
                    password TEXT NOT NULL,
                    role TEXT NOT NULL DEFAULT 'OWNER'
                        CHECK(role IN ('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN')),
                    company_id INTEGER,
                    assigned_caisse_code TEXT,
                    otp_code TEXT,
                    otp_expires_at DATETIME,
                    two_fa_enabled INTEGER NOT NULL DEFAULT 0,
                    remember_token TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    two_factor_secret TEXT,
                    two_factor_recovery_codes TEXT,
                    two_factor_confirmed_at DATETIME,
                    last_login_at DATETIME,
                    last_login_ip VARCHAR(45)
                )
            ");
            DB::statement("INSERT INTO users_new SELECT id, name, email, email_verified_at, password, CASE WHEN role = 'FIRM_ACCOUNTANT' THEN 'ACCOUNTANT' ELSE role END, company_id, assigned_caisse_code, otp_code, otp_expires_at, two_fa_enabled, remember_token, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, last_login_at, last_login_ip FROM users");
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('OWNER','ACCOUNTANT','CLERK','SUPER_ADMIN') NOT NULL DEFAULT 'OWNER'");
        }
    }
};
