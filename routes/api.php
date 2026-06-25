<?php

use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChartOfAccountsController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\FiscalYearController;
use App\Http\Controllers\Api\V1\BankReconciliationController;
use App\Http\Controllers\Api\V1\BankStatementController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\CustomerInvoiceController;
use App\Http\Controllers\Api\V1\DgiFiscalisExportController;
use App\Http\Controllers\Api\V1\DsfExportController;
use App\Http\Controllers\Api\V1\FinancialReportController;
use App\Http\Controllers\Api\V1\FixedAssetController;
use App\Http\Controllers\Api\V1\InvoicePdfController;
use App\Http\Controllers\Api\V1\InvoiceVerificationController;
use App\Http\Controllers\Api\V1\PayrollController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RecurringTransactionController;
use App\Http\Controllers\Api\V1\CnpsBordereauController;
use App\Http\Controllers\Api\V1\CustomerCreditNoteController;
use App\Http\Controllers\Api\V1\CustomerQuotationController;
use App\Http\Controllers\Api\V1\MailDeliveryController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\PatenteController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Http\Controllers\Api\V1\StatementController;
use App\Http\Controllers\Api\V1\StockMovementController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\SupplierCreditNoteController;
use App\Http\Controllers\Api\V1\SupplierInvoiceController;
use App\Http\Controllers\Api\V1\CashflowProjectionController;
use App\Http\Controllers\Api\V1\DeliveryNoteController;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\WithholdingCertificateController;
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

    // ── Auth (public) — rate-limited ────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->middleware('throttle:api-auth')->group(function () {
        Route::post('register',         [AuthController::class,       'register'])->name('register');
        Route::post('login',            [AuthController::class,       'login'])->name('login');
        Route::post('forgot-password',  [PasswordResetController::class, 'sendLink'])->name('forgot-password');
        Route::post('reset-password',   [PasswordResetController::class, 'reset'])->name('reset-password');
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
            // Email OTP / 2FA
            Route::post('otp/generate', [OtpController::class, 'generate'])->name('otp.generate');
            Route::post('otp/verify',   [OtpController::class, 'verify'])->name('otp.verify');
            Route::post('otp/disable',  [OtpController::class, 'disable'])->name('otp.disable');
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
                Route::post('customer-invoices/{invoice}/credit-note',         [CustomerInvoiceController::class, 'creditNote'])->name('customer-invoices.credit-note');
                Route::post('customer-invoices/{invoice}/record-withholding', [CustomerInvoiceController::class, 'recordWithholding'])->name('customer-invoices.record-withholding');
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

            // Supplier invoices (OWNER/ACCOUNTANT)
            Route::get('supplier-invoices',                          [SupplierInvoiceController::class, 'index'])->name('supplier-invoices.index');
            Route::get('supplier-invoices/{invoice}',                [SupplierInvoiceController::class, 'show'])->name('supplier-invoices.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('supplier-invoices',                     [SupplierInvoiceController::class, 'store'])->name('supplier-invoices.store');
                Route::post('supplier-invoices/{invoice}/pay',       [SupplierInvoiceController::class, 'pay'])->name('supplier-invoices.pay');
                Route::delete('supplier-invoices/{invoice}',         [SupplierInvoiceController::class, 'destroy'])->name('supplier-invoices.destroy');
            });

            // Fixed assets (OWNER/ACCOUNTANT)
            Route::get('fixed-assets',                               [FixedAssetController::class, 'index'])->name('fixed-assets.index');
            Route::get('fixed-assets/{asset}',                       [FixedAssetController::class, 'show'])->name('fixed-assets.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('fixed-assets',                          [FixedAssetController::class, 'store'])->name('fixed-assets.store');
                Route::post('fixed-assets/run-depreciation',         [FixedAssetController::class, 'runDepreciation'])->name('fixed-assets.run-depreciation');
                Route::post('fixed-assets/{asset}/dispose',          [FixedAssetController::class, 'dispose'])->name('fixed-assets.dispose');
            });

            // Bank reconciliation (OWNER/ACCOUNTANT)
            Route::get('reconciliation',                                          [BankReconciliationController::class, 'index'])->name('reconciliation.index');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('reconciliation',                                     [BankReconciliationController::class, 'store'])->name('reconciliation.store');
                Route::get('reconciliation/{session}',                            [BankReconciliationController::class, 'show'])->name('reconciliation.show');
                Route::post('reconciliation/{session}/lines/{line}/match',        [BankReconciliationController::class, 'matchLine'])->name('reconciliation.match');
                Route::post('reconciliation/{session}/close',                     [BankReconciliationController::class, 'close'])->name('reconciliation.close');
            });

            // Budgets (OWNER/ACCOUNTANT)
            Route::get('budgets',                                    [BudgetController::class, 'index'])->name('budgets.index');
            Route::get('budgets/{budget}',                           [BudgetController::class, 'show'])->name('budgets.show');
            Route::get('budgets/{budget}/variance',                  [BudgetController::class, 'variance'])->name('budgets.variance');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('budgets',                               [BudgetController::class, 'store'])->name('budgets.store');
                Route::delete('budgets/{budget}',                    [BudgetController::class, 'destroy'])->name('budgets.destroy');
            });

            // DSF / fiscal exports (OWNER/ACCOUNTANT)
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('exports/dsf',        [DsfExportController::class, 'generate'])->name('exports.dsf');
                Route::post('exports/tva-monthly',[DsfExportController::class, 'monthlyTva'])->name('exports.tva-monthly');
            });

            // Audit log (OWNER only)
            Route::get('audit-log', [AuditLogController::class, 'index'])
                ->middleware(\App\Http\Middleware\RequireRole::class . ':OWNER')
                ->name('audit-log.index');

            // Fiscal year operations (OWNER only)
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER')->group(function () {
                Route::post('fiscal-year/close',            [FiscalYearController::class, 'close'])->name('fiscal-year.close');
                Route::post('fiscal-year/opening-balances', [FiscalYearController::class, 'importOpeningBalances'])->name('fiscal-year.opening-balances');
            });

            // Chart of accounts
            Route::get('accounts', [ChartOfAccountsController::class, 'index'])->name('accounts.index');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('accounts',        [ChartOfAccountsController::class, 'store'])->name('accounts.store');
                Route::put('accounts/{account}', [ChartOfAccountsController::class, 'update'])->name('accounts.update');
            });

            // Customer & supplier statement PDFs (export-rate-limited)
            Route::middleware('throttle:api-export')->group(function () {
                Route::get('customers/{customer}/statement',  [StatementController::class, 'customerStatement'])->name('customers.statement');
                Route::get('suppliers/{supplier}/statement',  [StatementController::class, 'supplierStatement'])->name('suppliers.statement');
                Route::get('suppliers/{supplier}/withholding-certificate', [WithholdingCertificateController::class, 'generate'])->name('suppliers.withholding-cert');
                Route::post('payroll/cnps-bordereau', [CnpsBordereauController::class, 'generate'])->name('payroll.cnps-bordereau');
            });

            // Customer credit notes
            Route::get('customers/{customer}/credit-notes',        [CustomerCreditNoteController::class, 'index'])->name('customers.credit-notes.index');
            Route::get('customers/{customer}/credit-notes/{creditNote}', [CustomerCreditNoteController::class, 'show'])->name('customers.credit-notes.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('customers/{customer}/credit-notes',   [CustomerCreditNoteController::class, 'store'])->name('customers.credit-notes.store');
            });

            // Supplier credit notes
            Route::get('suppliers/{supplier}/credit-notes',        [SupplierCreditNoteController::class, 'index'])->name('suppliers.credit-notes.index');
            Route::get('suppliers/{supplier}/credit-notes/{creditNote}', [SupplierCreditNoteController::class, 'show'])->name('suppliers.credit-notes.show');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('suppliers/{supplier}/credit-notes',   [SupplierCreditNoteController::class, 'store'])->name('suppliers.credit-notes.store');
            });

            // Customer credit note PDFs
            Route::get('customers/{customer}/credit-notes/{creditNote}/pdf', [CustomerCreditNoteController::class, 'pdf'])->name('customers.credit-notes.pdf');
            // Supplier credit note PDFs
            Route::get('suppliers/{supplier}/credit-notes/{creditNote}/pdf', [SupplierCreditNoteController::class, 'pdf'])->name('suppliers.credit-notes.pdf');

            // Customer quotations / devis
            Route::get('quotations',                            [CustomerQuotationController::class, 'index'])->name('quotations.index');
            Route::get('quotations/{quotation}',                [CustomerQuotationController::class, 'show'])->name('quotations.show');
            Route::get('quotations/{quotation}/pdf',            [CustomerQuotationController::class, 'pdf'])->name('quotations.pdf');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('quotations',                       [CustomerQuotationController::class, 'store'])->name('quotations.store');
                Route::put('quotations/{quotation}/status',     [CustomerQuotationController::class, 'updateStatus'])->name('quotations.status');
                Route::post('quotations/{quotation}/convert',   [CustomerQuotationController::class, 'convert'])->name('quotations.convert');
                Route::delete('quotations/{quotation}',         [CustomerQuotationController::class, 'destroy'])->name('quotations.destroy');
            });

            // Purchase orders / bons de commande
            Route::get('purchase-orders',                       [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
            Route::get('purchase-orders/{purchaseOrder}',       [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
            Route::get('purchase-orders/{purchaseOrder}/pdf',   [PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('purchase-orders',                  [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
                Route::put('purchase-orders/{purchaseOrder}/status',   [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.status');
                Route::post('purchase-orders/{purchaseOrder}/receive',  [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
                Route::delete('purchase-orders/{purchaseOrder}',        [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
            });

            // Delivery notes / bons de livraison
            Route::get('delivery-notes',                            [DeliveryNoteController::class, 'index'])->name('delivery-notes.index');
            Route::get('delivery-notes/{deliveryNote}',             [DeliveryNoteController::class, 'show'])->name('delivery-notes.show');
            Route::get('delivery-notes/{deliveryNote}/pdf',         [DeliveryNoteController::class, 'pdf'])->name('delivery-notes.pdf');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('delivery-notes',                       [DeliveryNoteController::class, 'store'])->name('delivery-notes.store');
                Route::put('delivery-notes/{deliveryNote}/status',  [DeliveryNoteController::class, 'updateStatus'])->name('delivery-notes.status');
                Route::delete('delivery-notes/{deliveryNote}',      [DeliveryNoteController::class, 'destroy'])->name('delivery-notes.destroy');
            });

            // Cashflow projection (30/60/90 days)
            Route::get('cashflow/projection', [CashflowProjectionController::class, 'projection'])->name('cashflow.projection');

            // Subscription receipt PDF
            Route::get('subscriptions/receipt', [SubscriptionController::class, 'receipt'])->name('subscriptions.receipt');

            // Patente (local business tax)
            Route::get('patente',                               [PatenteController::class, 'index'])->name('patente.index');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('patente',                          [PatenteController::class, 'store'])->name('patente.store');
                Route::post('patente/{patenteRecord}/pay',      [PatenteController::class, 'pay'])->name('patente.pay');
                Route::delete('patente/{patenteRecord}',        [PatenteController::class, 'destroy'])->name('patente.destroy');
            });

            // Email delivery (throttle export)
            Route::middleware(['throttle:api-export', \App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT'])->group(function () {
                Route::post('mail/invoice/{invoice}',                      [MailDeliveryController::class, 'sendInvoice'])->name('mail.invoice');
                Route::post('mail/customer-statement/{customer}',          [MailDeliveryController::class, 'sendCustomerStatement'])->name('mail.customer-statement');
                Route::post('mail/supplier-statement/{supplier}',          [MailDeliveryController::class, 'sendSupplierStatement'])->name('mail.supplier-statement');
            });

            // Stock / inventory movements
            Route::get('stock',                            [StockMovementController::class, 'index'])->name('stock.index');
            Route::get('stock/ledger',                     [StockMovementController::class, 'ledger'])->name('stock.ledger');
            Route::get('stock/valuation',                  [StockMovementController::class, 'valuation'])->name('stock.valuation');
            Route::middleware(\App\Http\Middleware\RequireRole::class . ':OWNER,ACCOUNTANT')->group(function () {
                Route::post('stock',                       [StockMovementController::class, 'store'])->name('stock.store');
            });

            // CSV exports (all authenticated roles)
            Route::prefix('exports')->name('exports.')->group(function () {
                Route::get('trial-balance-csv',    [ExportController::class, 'trialBalanceCsv'])->name('trial-balance-csv');
                Route::get('journal-csv',          [ExportController::class, 'journalCsv'])->name('journal-csv');
                Route::get('aged-receivables-csv', [ExportController::class, 'agedReceivablesCsv'])->name('aged-receivables-csv');
                Route::get('aged-payables-csv',    [ExportController::class, 'agedPayablesCsv'])->name('aged-payables-csv');
            });
        });
    });
});
