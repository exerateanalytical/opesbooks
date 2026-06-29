<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\PlatformSetting;

class SyncInvoiceToDgiPortalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(protected int $journalEntryId) {}

    public function handle(): void
    {
        // Platform kill-switch: when DGI auto-sync is disabled, hold the entry
        // and retry later rather than télétransmitting.
        if (! PlatformSetting::flag('dgi_auto_sync', true)) {
            $this->release(300);
            return;
        }

        // Atomically claim this entry so concurrent workers can't double-
        // télétransmit it, and an already-APPROVED entry is never re-sent.
        // Only PENDING / REJECTED entries are claimable; the claim flips the
        // status to SYNCING in a single conditional UPDATE.
        $claimed = DB::table('journal_entries')
            ->where('id', $this->journalEntryId)
            ->whereIn('dgi_sync_status', ['PENDING', 'REJECTED'])
            ->update(['dgi_sync_status' => 'SYNCING', 'updated_at' => now()]);

        if ($claimed === 0) {
            return; // APPROVED, already SYNCING elsewhere, or entry gone
        }

        $entry = DB::table('journal_entries')
            ->join('companies', 'journal_entries.company_id', '=', 'companies.id')
            ->where('journal_entries.id', $this->journalEntryId)
            ->select('journal_entries.*', 'companies.niu', 'companies.name as company_name')
            ->first();

        if (! $entry) {
            return;
        }

        $lines = DB::table('journal_lines')
            ->join('syscohada_accounts', 'journal_lines.syscohada_account_id', '=', 'syscohada_accounts.id')
            ->where('journal_lines.journal_entry_id', $this->journalEntryId)
            ->select('syscohada_accounts.code', 'journal_lines.debit', 'journal_lines.credit')
            ->get();

        $baseHt    = 0.00;
        $totalTva  = 0.00;
        $totalCac  = 0.00;

        foreach ($lines as $line) {
            if (in_array($line->code, ['701100', '706000'])) {
                $baseHt += (float) $line->credit;
            }
            if ($line->code === '443100') {           // TVA collectée
                $totalTva += (float) $line->credit;
            }
            if ($line->code === '448600') {           // CAC (10% of TVA) — previously omitted
                $totalCac += (float) $line->credit;
            }
        }

        $totalTax = $totalTva + $totalCac;            // total tax now includes CAC
        $totalTtc = $baseHt + $totalTax;

        try {
            $response = Http::withHeaders([
                'X-Opes-App-Signature' => 'OpesBooks_v1',
                'Accept'               => 'application/json',
            ])->timeout(15)->post(config('services.dgi.endpoint', 'https://teledeclaration-dgi.cm/api/invoices'), [
                'taxpayer_niu'      => $entry->niu,
                'invoice_reference' => $entry->reference_id,
                'crypto_hash'       => $entry->invoice_crypto_hash,
                'amount_ht'         => $baseHt,
                'amount_tva'        => $totalTva,
                'amount_cac'        => $totalCac,
                'amount_tax'        => $totalTax,
                'amount_ttc'        => $totalTtc,
                'currency'          => 'XAF',
                'timestamp'         => now()->toIso8601String(),
            ]);

            if ($response->successful() && $response->json('status') === 'VALIDATED') {
                DB::table('journal_entries')->where('id', $this->journalEntryId)->update([
                    'dgi_validation_token' => $response->json('dgi_clearance_token'),
                    'dgi_validated_at'     => now(),
                    'dgi_sync_status'      => 'APPROVED',
                    'dgi_error_payload'    => null,
                    'updated_at'           => now(),
                ]);
            } else {
                DB::table('journal_entries')->where('id', $this->journalEntryId)->update([
                    'dgi_sync_status'   => 'REJECTED',
                    'dgi_error_payload' => $response->body(),
                    'updated_at'        => now(),
                ]);
            }
        } catch (\Exception $e) {
            DB::table('journal_entries')->where('id', $this->journalEntryId)->update([
                'dgi_sync_status'   => 'PENDING',
                'dgi_error_payload' => 'Network error: ' . $e->getMessage(),
                'updated_at'        => now(),
            ]);

            throw $e;
        }
    }
}
