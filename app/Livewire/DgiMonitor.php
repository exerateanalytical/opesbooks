<?php

namespace App\Livewire;

use App\Jobs\SyncInvoiceToDgiPortalJob;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DgiMonitor extends Component
{
    use WithPagination;

    public string $language     = 'FR';
    public string $searchQuery  = '';
    public string $statusFilter = 'ALL';

    protected $queryString = ['searchQuery', 'statusFilter'];

    public function updatedSearchQuery(): void  { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function toggleLanguage(): void
    {
        $this->language = $this->language === 'FR' ? 'EN' : 'FR';
    }

    public function retrySync(int $journalEntryId): void
    {
        $user = auth()->user();

        if ($user && ! in_array($user->role, ['OWNER', 'ACCOUNTANT'])) {
            session()->flash('error', $this->language === 'FR'
                ? 'Accès refusé. Droits insuffisants.'
                : 'Access denied. Insufficient privileges.');
            return;
        }

        SyncInvoiceToDgiPortalJob::dispatch($journalEntryId);

        session()->flash('success', $this->language === 'FR'
            ? 'Transmission DGI relancée avec succès.'
            : 'DGI transmission re-queued successfully.');
    }

    public function render()
    {
        $user      = auth()->user();
        $companyId = $user ? $user->company_id : DB::table('companies')->first()?->id;

        $query = DB::table('journal_entries')
            ->where('company_id', $companyId)
            ->orderBy('posting_date', 'desc');

        if ($this->statusFilter !== 'ALL') {
            $query->where('dgi_sync_status', $this->statusFilter);
        }

        if (! empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('reference_id', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('memo', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('dgi_validation_token', 'like', '%' . $this->searchQuery . '%');
            });
        }

        return view('livewire.dgi-monitor', [
            'entries' => $query->paginate(15),
        ])->layout('layouts.app');
    }
}
