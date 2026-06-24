<?php

use App\Livewire\TaxDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => view('pages.login'))->name('login');
Route::get('/app',   fn () => view('pages.app'))->name('app');

Route::get('/', function () {
    $company             = \App\Models\Company::first();
    $stats               = [];
    $recentTransactions  = [];
    $unresolvedCount     = 0;
    $pagination          = null;

    if ($company) {
        $revenueEntry  = \App\Models\JournalLine::whereHas('journalEntry', fn ($q) => $q->where('company_id', $company->id))
            ->whereHas('account', fn ($q) => $q->where('code', '701100'))
            ->sum('credit');

        $vatCollected  = \App\Models\JournalLine::whereHas('journalEntry', fn ($q) => $q->where('company_id', $company->id))
            ->whereHas('account', fn ($q) => $q->where('code', '443100'))
            ->sum('credit');

        $cacDue        = \App\Models\JournalLine::whereHas('journalEntry', fn ($q) => $q->where('company_id', $company->id))
            ->whereHas('account', fn ($q) => $q->where('code', '448600'))
            ->sum('credit');

        $totalExpenses = \App\Models\JournalLine::whereHas('journalEntry', fn ($q) => $q->where('company_id', $company->id))
            ->whereHas('account', fn ($q) => $q->whereBetween('class_digit', [6, 6]))
            ->sum('debit');

        $stats = [
            'revenue_ht'     => $revenueEntry,
            'vat_collected'  => $vatCollected,
            'cac_due'        => $cacDue,
            'total_expenses' => $totalExpenses,
        ];
    }

    return view('pages.dashboard', compact('company', 'stats', 'recentTransactions', 'unresolvedCount', 'pagination'));
});

Route::get('/tax-dashboard', TaxDashboard::class)->name('tax.dashboard');
