<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BankStatementController;
use App\Jobs\SyncInvoiceToDgiPortalJob;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\DgiFiscalisExportController;
use App\Http\Controllers\Api\V1\InvoicePdfController;
use App\Http\Controllers\Api\V1\InvoiceVerificationController;
use App\Http\Controllers\Api\V1\LedgerController;
use App\Http\Controllers\Api\V1\ManualJournalController;
use App\Http\Controllers\Api\V1\OfflineSyncController;
use App\Http\Controllers\Api\V1\SubledgerController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\TaxCalculatorController;
use App\Http\Controllers\Api\V1\TelecomCallbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {

    // ── Auth (public) ────────────────────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login',    [AuthController::class, 'login'])->name('login');
    });

    // ── Tax calculator utility (stateless, public) ───────────────────────────
    Route::prefix('tax')->name('tax.')->group(function () {
        Route::post('from-ht',  [TaxCalculatorController::class, 'fromHt'])->name('from-ht');
        Route::post('from-ttc', [TaxCalculatorController::class, 'fromTtc'])->name('from-ttc');
    });

    // ── Async telecom ingestion (public webhook endpoint) ────────────────────
    Route::post('ingest/telecom/callback', [TelecomCallbackController::class, 'handle'])
        ->name('ingest.telecom');

    // ── Public invoice hash verification (for DGI QR scan) ──────────────────
    Route::get('verify/invoice/{hash}', [InvoiceVerificationController::class, 'verify'])
        ->name('verify.invoice');

    // ── Authenticated routes ─────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', \App\Http\Middleware\RequireActiveSubscription::class])
        ->group(function () {

        // Auth utilities
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('logout',    [AuthController::class, 'logout'])->name('logout');
            Route::get('me',         [AuthController::class, 'me'])->name('me');
            Route::get('users',      [AuthController::class, 'team'])->name('team');
            Route::post('users',     [AuthController::class, 'invite'])
                ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER')
                ->name('invite');
        });

        // Company management (OWNER/ACCOUNTANT only)
        Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')
            ->group(function () {
            Route::apiResource('companies', CompanyController::class);
        });

        // Manual journal (OWNER/ACCOUNTANT)
        Route::post('journal/manual', [ManualJournalController::class, 'store'])
            ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')
            ->name('journal.manual');

        // Offline sync
        Route::prefix('sync')->name('sync.')->group(function () {
            Route::post('push',    [OfflineSyncController::class, 'push'])->name('push');
            Route::get('pull',     [OfflineSyncController::class, 'pull'])->name('pull');
            Route::get('status',   [OfflineSyncController::class, 'status'])->name('status');
        });

        // Per-company routes
        Route::prefix('companies/{company}')->name('companies.')->group(function () {

            // Ledger & reporting (all authenticated roles)
            Route::get('ledger',        [LedgerController::class, 'entries'])->name('ledger');
            Route::get('trial-balance', [LedgerController::class, 'trialBalance'])->name('trial-balance');

            // Invoice PDF generation
            Route::post('invoice/generate',          [InvoicePdfController::class, 'generate'])->name('invoice.generate');
            Route::get('invoice/{entry}/download',   [InvoicePdfController::class, 'download'])->name('invoice.download');

            // Logo upload (OWNER only)
            Route::post('logo', [CompanyController::class, 'uploadLogo'])
                ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER')
                ->name('logo.upload');

            // OWNER/ACCOUNTANT-only operations
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')
                ->group(function () {

                // DGI Fiscalis export
                Route::get('exports/dgi-fiscalis', [DgiFiscalisExportController::class, 'export'])
                    ->name('exports.dgi-fiscalis');

                // DGI Live-Link: force re-sync of a specific journal entry
                Route::post('journal/{id}/dgi-sync', function (\Illuminate\Http\Request $request, int $id) {
                    SyncInvoiceToDgiPortalJob::dispatch($id);
                    return response()->json(['status' => 'queued', 'message' => 'DGI sync job dispatched.'], 202);
                })->name('journal.dgi-sync');

                // Bank statement CSV import
                Route::post('bank-statement/import', [BankStatementController::class, 'import'])
                    ->name('bank-statement.import');

                // Subscription billing
                Route::post('subscriptions/initiate', [SubscriptionController::class, 'initiate'])
                    ->name('subscriptions.initiate');
                Route::post('subscriptions/confirm',  [SubscriptionController::class, 'confirm'])
                    ->name('subscriptions.confirm');
                Route::get('subscriptions/status',    [SubscriptionController::class, 'status'])
                    ->name('subscriptions.status');
            });

            // Dynamic sub-ledger provisioning (all roles)
            Route::get('subledgers',  [SubledgerController::class, 'list'])->name('subledgers.list');
            Route::post('subledgers', [SubledgerController::class, 'provision'])
                ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')
                ->name('subledgers.provision');
        });
    });
});
