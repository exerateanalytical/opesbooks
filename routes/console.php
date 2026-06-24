<?php

use App\Services\RecurringTransactionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(RecurringTransactionService::class)->processDue();
})->dailyAt('06:00')->name('recurring-transactions')->withoutOverlapping();
