<?php

use App\Livewire\DgiMonitor;
use App\Livewire\TaxDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/login',      fn () => view('pages.login'))->name('login');
Route::get('/app',        fn () => view('pages.app'))->name('app');
Route::get('/onboarding', fn () => view('pages.onboarding'))->name('onboarding');
Route::get('/offline',    fn () => view('offline'))->name('offline');
Route::get('/about', fn () => view('pages.about'))->name('about');

// Public marketing site (conversion funnel)
Route::get('/',                [\App\Http\Controllers\MarketingController::class, 'home'])->name('home');
Route::get('/fonctionnalites',        [\App\Http\Controllers\MarketingController::class, 'features'])->name('m.features');
Route::get('/fonctionnalites/{slug}', [\App\Http\Controllers\MarketingController::class, 'feature'])->name('m.feature');
Route::get('/tarifs',          [\App\Http\Controllers\MarketingController::class, 'pricing'])->name('m.pricing');
Route::get('/contact',         [\App\Http\Controllers\MarketingController::class, 'contact'])->name('m.contact');
Route::get('/a-propos',        [\App\Http\Controllers\MarketingController::class, 'about'])->name('m.about');
Route::get('/faq',             [\App\Http\Controllers\MarketingController::class, 'faq'])->name('m.faq');
Route::get('/conditions',      [\App\Http\Controllers\MarketingController::class, 'terms'])->name('m.terms');
Route::get('/confidentialite', [\App\Http\Controllers\MarketingController::class, 'privacy'])->name('m.privacy');
Route::post('/contact',        [\App\Http\Controllers\MarketingController::class, 'contactSubmit'])->name('m.contact.submit');
Route::get('/blog',            [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}',     [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Developer portal — OpenAPI docs + Swagger UI
Route::get('/developer',         fn () => view('api-docs'))->name('developer');
Route::get('/docs',              fn () => view('api-docs'))->name('docs');
Route::get('/developer/postman', fn () => response()->download(public_path('postman/opesbooks.postman_collection.json')))->name('developer.postman');

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
        Route::get('/impersonate/leave',          [\App\Http\Controllers\Admin\AdminDashboardController::class, 'leaveImpersonation'])->name('impersonate.leave');

        // Subscriptions & billing
        Route::get('/subscriptions',             [\App\Http\Controllers\Admin\AdminInsightsController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/billing',                   [\App\Http\Controllers\Admin\AdminInsightsController::class, 'billing'])->name('billing');
        Route::post('/companies/{company}/payments', [\App\Http\Controllers\Admin\AdminInsightsController::class, 'recordPayment'])->name('payments.record');
        Route::get('/payments/{payment}/receipt',    [\App\Http\Controllers\Admin\AdminInsightsController::class, 'receipt'])->name('payments.receipt');

        // API product
        Route::get('/api-keys',                  [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'index'])->name('api-keys');
        Route::post('/api-keys',                 [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'store'])->name('api-keys.store');
        Route::post('/api-keys/{apiKey}/revoke', [\App\Http\Controllers\Admin\AdminApiKeyController::class, 'revoke'])->name('api-keys.revoke');
        Route::get('/api-logs',                  [\App\Http\Controllers\Admin\AdminApiLogController::class, 'index'])->name('api-logs');
        Route::get('/api-docs',                  fn () => view('admin.api-docs'))->name('api-docs');

        // Platform ops
        Route::get('/system',                    [\App\Http\Controllers\Admin\AdminInsightsController::class, 'system'])->name('system');
        Route::post('/system/retry-jobs',        [\App\Http\Controllers\Admin\AdminInsightsController::class, 'retryFailedJobs'])->name('system.retry-jobs');
        Route::post('/system/flush-jobs',        [\App\Http\Controllers\Admin\AdminInsightsController::class, 'flushFailedJobs'])->name('system.flush-jobs');
        Route::get('/audit',                     [\App\Http\Controllers\Admin\AdminInsightsController::class, 'audit'])->name('audit');
        Route::get('/announcements',             [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'index'])->name('announcements');
        Route::post('/announcements',            [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'store'])->name('announcements.store');
        Route::post('/announcements/{announcement}/toggle', [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'toggle'])->name('announcements.toggle');
        Route::delete('/announcements/{announcement}',      [\App\Http\Controllers\Admin\AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');

        // Platform settings / feature flags
        Route::get('/settings',  [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('settings.update');
        Route::get('/feature-flags',          [\App\Http\Controllers\Admin\AdminFeatureFlagController::class, 'index'])->name('feature-flags');
        Route::post('/feature-flags/{flag}',  [\App\Http\Controllers\Admin\AdminFeatureFlagController::class, 'update'])->name('feature-flags.update');
    });
});
