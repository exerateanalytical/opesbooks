<?php

use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BankStatementController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\CustomerInvoiceController;
use App\Http\Controllers\Api\V1\DgiFiscalisExportController;
use App\Http\Controllers\Api\V1\FinancialReportController;
use App\Http\Controllers\Api\V1\InvoicePdfController;
use App\Http\Controllers\Api\V1\InvoiceVerificationController;
use App\Http\Controllers\Api\V1\PayrollController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RecurringTransactionController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Jobs\SyncInvoiceToDgiPortalJob;
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
            Route::put('profile',    [ProfileController::class, 'update'])->name('profile.update');
            Route::put('password',   [ProfileController::class, 'changePassword'])->name('password.change');
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

                // Financial reports
                Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('pl',                [FinancialReportController::class, 'profitAndLoss'])->name('pl');
                    Route::get('balance-sheet',     [FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
                    Route::get('cash-flow',         [FinancialReportController::class, 'cashFlow'])->name('cash-flow');
                    Route::get('aged-receivables',  [FinancialReportController::class, 'agedReceivables'])->name('aged-receivables');
                    Route::get('aged-payables',     [FinancialReportController::class, 'agedPayables'])->name('aged-payables');
                });

                // Recurring transactions
                Route::prefix('recurring')->name('recurring.')->group(function () {
                    Route::get('',        [RecurringTransactionController::class, 'index'])->name('index');
                    Route::post('',       [RecurringTransactionController::class, 'store'])->name('store');
                    Route::put('{rt}',    [RecurringTransactionController::class, 'update'])->name('update');
                    Route::delete('{rt}', [RecurringTransactionController::class, 'destroy'])->name('destroy');
                    Route::post('run-now',[RecurringTransactionController::class, 'runNow'])->name('run-now');
                });

                // Payroll
                Route::prefix('payroll')->name('payroll.')->group(function () {
                    Route::get('employees',                      [PayrollController::class, 'employees'])->name('employees.index');
                    Route::post('employees',                     [PayrollController::class, 'storeEmployee'])->name('employees.store');
                    Route::put('employees/{employee}',           [PayrollController::class, 'updateEmployee'])->name('employees.update');
                    Route::get('periods',                        [PayrollController::class, 'periods'])->name('periods.index');
                    Route::post('periods',                       [PayrollController::class, 'calculate'])->name('periods.calculate');
                    Route::post('periods/{period}/post',         [PayrollController::class, 'post'])->name('periods.post');
                });
            });

            // Customers (OWNER/ACCOUNTANT/CLERK can read; OWNER/ACCOUNTANT can write)
            Route::get('customers',           [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}',[CustomerController::class, 'show'])->name('customers.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('customers',              [CustomerController::class, 'store'])->name('customers.store');
                Route::put('customers/{customer}',    [CustomerController::class, 'update'])->name('customers.update');
                Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            });

            // Suppliers
            Route::get('suppliers',           [SupplierController::class, 'index'])->name('suppliers.index');
            Route::get('suppliers/{supplier}',[SupplierController::class, 'show'])->name('suppliers.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('suppliers',              [SupplierController::class, 'store'])->name('suppliers.store');
                Route::put('suppliers/{supplier}',    [SupplierController::class, 'update'])->name('suppliers.update');
                Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
            });

            // Customer invoices
            Route::get('customer-invoices',                              [CustomerInvoiceController::class, 'index'])->name('customer-invoices.index');
            Route::get('customer-invoices/{invoice}',                    [CustomerInvoiceController::class, 'show'])->name('customer-invoices.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('customer-invoices',                         [CustomerInvoiceController::class, 'store'])->name('customer-invoices.store');
                Route::post('customer-invoices/{invoice}/send',          [CustomerInvoiceController::class, 'markSent'])->name('customer-invoices.send');
                Route::post('customer-invoices/{invoice}/pay',           [CustomerInvoiceController::class, 'markPaid'])->name('customer-invoices.pay');
                Route::post('customer-invoices/{invoice}/credit-note',   [CustomerInvoiceController::class, 'creditNote'])->name('customer-invoices.credit-note');
            });
            Route::get('aged-receivables', [CustomerInvoiceController::class, 'agedReceivables'])->name('aged-receivables');

            // Journal entry attachments
            Route::get('journal/{entry}/attachments',                    [AttachmentController::class, 'index'])->name('attachments.index');
            Route::post('journal/{entry}/attachments',                   [AttachmentController::class, 'store'])->name('attachments.store');
            Route::delete('journal/{entry}/attachments/{attachment}',    [AttachmentController::class, 'destroy'])->name('attachments.destroy');

            // Dynamic sub-ledger provisioning (all roles)
            Route::get('subledgers',  [SubledgerController::class, 'list'])->name('subledgers.list');
            Route::post('subledgers', [SubledgerController::class, 'provision'])
                ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')
                ->name('subledgers.provision');
        });
    });
});
