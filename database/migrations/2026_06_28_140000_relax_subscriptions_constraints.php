<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The subscriptions table hardcoded plan to ('STARTER','GROWTH','ENTERPRISE') —
 * values that don't match the dynamic PlanConfig slugs (free/starter/business/
 * enterprise) — locked status to a set missing SUSPENDED, and forced
 * billing_phone NOT NULL (so admin-created subscriptions couldn't be saved).
 * This relaxes plan to a free-form varchar, expands the status set, and makes
 * billing_phone nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
            DB::statement("
                CREATE TABLE subscriptions_new (
                    id integer primary key autoincrement not null,
                    company_id integer not null,
                    plan varchar not null default 'free',
                    amount_xaf numeric not null,
                    billing_phone varchar,
                    status varchar check (status in ('ACTIVE','PENDING','PAST_DUE','SUSPENDED','CANCELLED')) not null default 'PENDING',
                    aggregator_reference varchar,
                    period_start date not null,
                    period_end date not null,
                    created_at datetime,
                    updated_at datetime,
                    foreign key(company_id) references companies(id) on delete cascade
                )
            ");
            DB::statement('
                INSERT INTO subscriptions_new (id, company_id, plan, amount_xaf, billing_phone, status, aggregator_reference, period_start, period_end, created_at, updated_at)
                SELECT id, company_id, plan, amount_xaf, billing_phone, status, aggregator_reference, period_start, period_end, created_at, updated_at FROM subscriptions
            ');
            DB::statement('DROP TABLE subscriptions');
            DB::statement('ALTER TABLE subscriptions_new RENAME TO subscriptions');
            DB::statement('PRAGMA foreign_keys=on');
        } else {
            // MySQL / MariaDB
            DB::statement("ALTER TABLE subscriptions MODIFY plan VARCHAR(255) NOT NULL DEFAULT 'free'");
            DB::statement('ALTER TABLE subscriptions MODIFY billing_phone VARCHAR(255) NULL');
            DB::statement("ALTER TABLE subscriptions MODIFY status VARCHAR(20) NOT NULL DEFAULT 'PENDING'");
        }
    }

    public function down(): void
    {
        // Non-reversible constraint tightening is intentionally a no-op:
        // re-imposing the old narrow CHECK could reject existing rows.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE subscriptions MODIFY billing_phone VARCHAR(255) NOT NULL');
        }
    }
};
