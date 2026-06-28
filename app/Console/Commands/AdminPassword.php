<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminPassword extends Command
{
    protected $signature = 'admin:password {email} {--password=} {--name=}';

    protected $description = 'Break-glass: create or reset a SUPER_ADMIN account password (no email required)';

    public function handle(): int
    {
        $email    = $this->argument('email');
        $password = $this->option('password') ?: Str::password(16);

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['password' => $password, 'role' => 'SUPER_ADMIN']);
            $user->tokens()->delete();
            $this->info("Reset password for existing admin {$email}.");
        } else {
            User::create([
                'name'       => $this->option('name') ?: 'Platform Admin',
                'email'      => $email,
                'password'   => $password, // 'hashed' cast
                'role'       => 'SUPER_ADMIN',
                'company_id' => null,
            ]);
            $this->info("Created new SUPER_ADMIN {$email}.");
        }

        $this->newLine();
        $this->line("  Email:    {$email}");
        $this->line("  Password: {$password}");
        $this->newLine();
        $this->warn('Store this password now — it will not be shown again.');

        return self::SUCCESS;
    }
}
