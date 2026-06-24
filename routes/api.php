<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\LedgerController;
use App\Http\Controllers\Api\V1\ManualJournalController;
use App\Http\Controllers\Api\V1\TaxCalculatorController;
use App\Http\Controllers\Api\V1\TelecomCallbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {

    // Company management
    Route::apiResource('companies', CompanyController::class);

    // Tax calculator utility (stateless)
    Route::prefix('tax')->name('tax.')->group(function () {
        Route::post('from-ht',  [TaxCalculatorController::class, 'fromHt'])->name('from-ht');
        Route::post('from-ttc', [TaxCalculatorController::class, 'fromTtc'])->name('from-ttc');
    });

    // Dual-stream ingestion
    Route::post('ingest/telecom/callback', [TelecomCallbackController::class, 'handle'])->name('ingest.telecom');
    Route::post('journal/manual',          [ManualJournalController::class, 'store'])->name('journal.manual');

    // Ledger queries per company
    Route::prefix('companies/{company}')->name('companies.')->group(function () {
        Route::get('ledger',        [LedgerController::class, 'entries'])->name('ledger');
        Route::get('trial-balance', [LedgerController::class, 'trialBalance'])->name('trial-balance');
    });
});
