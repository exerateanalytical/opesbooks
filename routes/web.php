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
