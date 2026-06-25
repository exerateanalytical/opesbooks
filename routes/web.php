<?php

use App\Livewire\DgiMonitor;
use App\Livewire\TaxDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => view('pages.login'))->name('login');
Route::get('/app',   fn () => view('pages.app'))->name('app');
Route::get('/about', fn () => view('pages.about'))->name('about');

// Root redirects to SPA — the authenticated SPA is the primary interface
Route::get('/', fn () => redirect('/app'));

Route::get('/tax-dashboard', TaxDashboard::class)->name('tax.dashboard');
Route::get('/dgi-monitor',   DgiMonitor::class)->name('dgi.monitor');

// Platform admin — SUPER_ADMIN only
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',   [\App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [\App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('logout');
    Route::middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/',                          [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users',                     [\App\Http\Controllers\Admin\AdminDashboardController::class, 'users'])->name('users');

        // Tenants
        Route::get('/companies',                 [\App\Http\Controllers\Admin\AdminInsightsController::class, 'companies'])->name('companies');
        Route::get('/companies/{company}',       [\App\Http\Controllers\Admin\AdminDashboardController::class, 'company'])->name('company');
        Route::post('/companies/{company}/subscription', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSubscription'])->name('company.subscription');
        Route::post('/impersonate/{user}',       [\App\Http\Controllers\Admin\AdminDashboardController::class, 'impersonate'])->name('impersonate');

        // Subscriptions & billing
        Route::get('/subscriptions',             [\App\Http\Controllers\Admin\AdminInsightsController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/billing',                   [\App\Http\Controllers\Admin\AdminInsightsController::class, 'billing'])->name('billing');

        // API product
        Route::get('/api-keys',                  [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'index'])->name('api-keys');
        Route::post('/api-keys',                 [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'store'])->name('api-keys.store');
        Route::post('/api-keys/{apiKey}/revoke', [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'revoke'])->name('api-keys.revoke');
        Route::get('/api-logs',                  [\App\Http\Controllers\Admin\AdminApiLogController::class, 'index'])->name('api-logs');
        Route::get('/api-docs',                  fn () => view('admin.api-docs'))->name('api-docs');

        // Platform ops
        Route::get('/system',                    [\App\Http\Controllers\Admin\AdminInsightsController::class, 'system'])->name('system');
        Route::get('/audit',                     [\App\Http\Controllers\Admin\AdminInsightsController::class, 'audit'])->name('audit');
        Route::get('/announcements',             [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements',            [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'store'])->name('announcements.store');
        Route::post('/announcements/{announcement}/toggle', [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'toggle'])->name('announcements.toggle');
        Route::delete('/announcements/{announcement}',      [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

        // Platform settings / feature flags
        Route::get('/settings',  [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('settings.update');
    });
});
