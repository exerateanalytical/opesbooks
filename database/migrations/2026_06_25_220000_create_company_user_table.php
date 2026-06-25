<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('OWNER');      // per-company role
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'company_id']);
        });

        // Backfill membership from the existing one-to-one users.company_id.
        // users.company_id remains the "active company" pointer.
        $users = DB::table('users')->whereNotNull('company_id')->get(['id', 'company_id', 'role']);
        $now = now();
        foreach ($users as $u) {
            DB::table('company_user')->insert([
                'user_id'    => $u->id,
                'company_id' => $u->company_id,
                'role'       => $u->role ?? 'OWNER',
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
