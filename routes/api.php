<?php

use App\Http\Controllers\Api\V1\BankStatementController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\DgiFiscalisExportController;
use App\Http\Controllers\Api\V1\InvoiceVerificationController;
use App\Http\Controllers\Api\V1\LedgerController;
use App\Http\Controllers\Api\V1\ManualJournalController;
use App\Http\Controllers\Api\V1\SubledgerController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\TaxCalculatorController;
use App\Http\Controllers\Api\V1\TelecomCallbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {

    // ── Company management ───────────────────────────────────────────────────
    Route::apiResource('companies', CompanyController::class);

    // ── Tax calculator utility (stateless) ───────────────────────────────────
    Route::prefix('tax')->name('tax.')->group(function () {
        Route::post('from-ht',  [TaxCalculatorController::class, 'fromHt'])->name('from-ht');
        Route::post('from-ttc', [TaxCalculatorController::class, 'fromTtc'])->name('from-ttc');
    });

    // ── Async telecom ingestion (returns 202 immediately, queues processing) ─
    Route::post('ingest/telecom/callback', [TelecomCallbackController::class, 'handle'])
        ->name('ingest.telecom');

    // ── Manual double-entry journal ──────────────────────────────────────────
    Route::post('journal/manual', [ManualJournalController::class, 'store'])
        ->name('journal.manual');

    // ── Public invoice hash verification (for DGI QR scan) ──────────────────
    Route::get('verify/invoice/{hash}', [InvoiceVerificationController::class, 'verify'])
        ->name('verify.invoice');

    // ── Per-company routes ───────────────────────────────────────────────────
    Route::prefix('companies/{company}')->name('companies.')->group(function () {

        // Ledger & reporting
        Route::get('ledger',        [LedgerController::class, 'entries'])->name('ledger');
        Route::get('trial-balance', [LedgerController::class, 'trialBalance'])->name('trial-balance');

        // DGI Fiscalis export
        Route::get('exports/dgi-fiscalis', [DgiFiscalisExportController::class, 'export'])
            ->name('exports.dgi-fiscalis');

        // Bank statement CSV import
        Route::post('bank-statement/import', [BankStatementController::class, 'import'])
            ->name('bank-statement.import');

        // Dynamic sub-ledger provisioning
        Route::get('subledgers',      [SubledgerController::class, 'list'])->name('subledgers.list');
        Route::post('subledgers',     [SubledgerController::class, 'provision'])->name('subledgers.provision');

        // Subscription billing
        Route::post('subscriptions/initiate', [SubscriptionController::class, 'initiate'])
            ->name('subscriptions.initiate');
        Route::post('subscriptions/confirm',  [SubscriptionController::class, 'confirm'])
            ->name('subscriptions.confirm');
        Route::get('subscriptions/status',    [SubscriptionController::class, 'status'])
            ->name('subscriptions.status');
    });
});
