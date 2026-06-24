<?php

namespace App\Providers;

use App\Services\CameroonTaxEngine;
use App\Services\CnpsIrppService;
use App\Services\FinancialStatementService;
use App\Services\FiscalGeographyRouter;
use App\Services\JournalPostingService;
use App\Services\MomoIngestionService;
use App\Services\OfflineSyncService;
use App\Services\ProrataVatService;
use App\Services\BudgetService;
use App\Services\DsfExportService;
use App\Services\FixedAssetService;
use App\Services\RecurringTransactionService;
use App\Services\SupplierInvoiceService;
use App\Services\TelecomReversalService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CameroonTaxEngine::class);
        $this->app->singleton(FiscalGeographyRouter::class);
        $this->app->singleton(ProrataVatService::class);
        $this->app->singleton(JournalPostingService::class);

        $this->app->singleton(MomoIngestionService::class, function ($app) {
            return new MomoIngestionService($app->make(JournalPostingService::class));
        });

        $this->app->singleton(OfflineSyncService::class, function ($app) {
            return new OfflineSyncService($app->make(JournalPostingService::class));
        });

        $this->app->singleton(TelecomReversalService::class, function ($app) {
            return new TelecomReversalService($app->make(JournalPostingService::class));
        });

        $this->app->singleton(RecurringTransactionService::class, function ($app) {
            return new RecurringTransactionService($app->make(JournalPostingService::class));
        });

        $this->app->singleton(CnpsIrppService::class);
        $this->app->singleton(FinancialStatementService::class);

        $this->app->singleton(SupplierInvoiceService::class, function ($app) {
            return new SupplierInvoiceService(
                $app->make(JournalPostingService::class),
                $app->make(FiscalGeographyRouter::class),
                $app->make(CameroonTaxEngine::class),
                $app->make(ProrataVatService::class),
            );
        });

        $this->app->singleton(FixedAssetService::class, function ($app) {
            return new FixedAssetService($app->make(JournalPostingService::class));
        });

        $this->app->singleton(DsfExportService::class, function ($app) {
            return new DsfExportService($app->make(FinancialStatementService::class));
        });

        $this->app->singleton(BudgetService::class);
    }

    public function boot(): void
    {
        // Map x-app-layout to the layouts/app.blade.php anonymous component
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layout');
    }
}
