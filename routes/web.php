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
        Route::get('/companies/{company}',       [\App\Http\Controllers\Admin\AdminDashboardController::class, 'company'])->name('company');
        Route::post('/companies/{company}/subscription', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSubscription'])->name('company.subscription');
        Route::post('/impersonate/{user}',       [\App\Http\Controllers\Admin\AdminDashboardController::class, 'impersonate'])->name('impersonate');
    });
});
