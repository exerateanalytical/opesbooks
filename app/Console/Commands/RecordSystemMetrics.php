<?php

namespace App\Console\Commands;

use App\Models\SystemMetric;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecordSystemMetrics extends Command
{
    protected $signature = 'metrics:record';
    protected $description = 'Capture platform metrics for the admin System Health charts';

    public function handle(): int
    {
        $now = now();

        $metrics = [
            'memory_usage_bytes' => memory_get_usage(true),
            'active_users_count' => User::where('updated_at', '>=', $now->copy()->subMinutes(15))->count(),
            'queue_size'         => Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0,
            'failed_jobs'        => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
        ];

        // DB ping latency
        $start = microtime(true);
        try { DB::select('SELECT 1'); } catch (\Throwable) {}
        $metrics['db_latency_ms'] = round((microtime(true) - $start) * 1000, 2);

        foreach ($metrics as $name => $value) {
            SystemMetric::create(['metric_name' => $name, 'metric_value' => $value, 'recorded_at' => $now]);
        }

        // Keep the table lean — drop rows older than 7 days.
        SystemMetric::where('recorded_at', '<', $now->copy()->subDays(7))->delete();

        $this->info('System metrics recorded.');
        return self::SUCCESS;
    }
}
