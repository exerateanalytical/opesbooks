<?php

use App\Services\FixedAssetService;
use App\Services\RecurringTransactionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(RecurringTransactionService::class)->processDue();
})->dailyAt('06:00')->name('recurring-transactions')->withoutOverlapping();

// Mark overdue supplier invoices daily
Schedule::call(function () {
    DB::table('supplier_invoices')
        ->whereIn('status', ['RECEIVED', 'APPROVED'])
        ->where('due_date', '<', now()->toDateString())
        ->update(['status' => 'OVERDUE', 'updated_at' => now()]);
})->dailyAt('00:05')->name('supplier-invoice-overdue')->withoutOverlapping();

// Run monthly depreciation on the 1st of every month at 03:00
Schedule::call(function () {
    $now = now();
    app(FixedAssetService::class)->runMonthlyDepreciation($now->month, $now->year);
})->monthlyOn(1, '03:00')->name('monthly-depreciation')->withoutOverlapping();
