<?php

namespace App\Services;

use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\RawPayloadQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Offline Sync Engine — replaces NativePHP (unsupported on Laravel 13).
 *
 * Architecture: The desktop/PWA client uses a local SQLite DB for all writes
 * when offline. On reconnect, it pushes a sync bundle to POST /api/v1/sync/push.
 * This service validates, deduplicates, and merges the bundle into MySQL.
 *
 * Conflict resolution rule: remote MySQL wins for ADJUSTMENT entries;
 * local client wins for STANDARD entries if not already present in MySQL.
 */
class OfflineSyncService
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * Process a sync bundle from the offline client.
     *
     * Bundle format:
     * {
     *   "client_id": "device-uuid",
     *   "synced_at": "2026-06-24T10:00:00Z",
     *   "journal_entries": [...],    // array of entry+lines objects
     *   "raw_payloads": [...]        // unprocessed telecom payloads
     * }
     *
     * Returns a summary of what was applied, skipped, and conflicted.
     */
    public function processBundle(Company $company, array $bundle): array
    {
        $results = [
            'client_id'   => $bundle['client_id'] ?? 'unknown',
            'applied'     => 0,
            'skipped'     => 0,
            'conflicts'   => 0,
            'errors'      => [],
            'synced_at'   => now()->toIso8601String(),
        ];

        DB::transaction(function () use ($company, $bundle, &$results) {
            foreach ($bundle['journal_entries'] ?? [] as $entryData) {
                $this->syncEntry($company, $entryData, $results);
            }

            foreach ($bundle['raw_payloads'] ?? [] as $payload) {
                $this->syncRawPayload($company, $payload, $results);
            }
        });

        return $results;
    }

    /**
     * Pull changes from MySQL since a given checkpoint for the client to apply locally.
     *
     * Returns entries newer than $sinceTimestamp for the company,
     * enabling the client SQLite to stay current after reconnect.
     */
    public function pullDelta(Company $company, string $sinceTimestamp): array
    {
        $since = Carbon::parse($sinceTimestamp);

        $entries = JournalEntry::where('company_id', $company->id)
            ->where('updated_at', '>', $since)
            ->with(['lines.account'])
            ->get();

        return [
            'company_id'     => $company->id,
            'delta_since'    => $sinceTimestamp,
            'pulled_at'      => now()->toIso8601String(),
            'entry_count'    => $entries->count(),
            'journal_entries'=> $entries->map(fn ($e) => $this->serializeEntry($e))->values()->toArray(),
        ];
    }

    /**
     * Returns a lightweight sync status for the client to decide whether a push/pull is needed.
     */
    public function status(Company $company): array
    {
        $lastEntry = JournalEntry::where('company_id', $company->id)
            ->orderByDesc('updated_at')
            ->first();

        return [
            'company_id'         => $company->id,
            'last_server_entry'  => $lastEntry?->updated_at?->toIso8601String(),
            'total_entries'      => JournalEntry::where('company_id', $company->id)->count(),
            'pending_queue'      => RawPayloadQueue::where('status', 'QUEUED')->count(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function syncEntry(Company $company, array $entryData, array &$results): void
    {
        $refId = $entryData['reference_id'] ?? null;
        if (! $refId) {
            $results['errors'][] = 'Entry missing reference_id — skipped.';
            $results['skipped']++;
            return;
        }

        // Idempotency: skip if already in MySQL
        if (JournalEntry::where('reference_id', $refId)->exists()) {
            $results['skipped']++;
            return;
        }

        try {
            $lines = array_map(fn ($l) => [
                'account_code' => $l['account_code'],
                'debit'        => (string) ($l['debit'] ?? '0'),
                'credit'       => (string) ($l['credit'] ?? '0'),
                'description'  => $l['description'] ?? null,
            ], $entryData['lines'] ?? []);

            $this->poster->post([
                'company_id'         => $company->id,
                'posting_date'       => $entryData['posting_date'],
                'posting_type'       => $entryData['posting_type'] ?? 'STANDARD',
                'reference_id'       => $refId,
                'source_pipeline'    => $entryData['source_pipeline'] ?? 'OFFLINE_SYNC',
                'transaction_status' => $entryData['transaction_status'] ?? 'SUCCESSFUL',
                'memo'               => $entryData['memo'] ?? 'Synced from offline client',
                'invoice_crypto_hash'=> $entryData['invoice_crypto_hash'] ?? null,
            ], $lines);

            $results['applied']++;
        } catch (\Throwable $e) {
            Log::warning('OfflineSync entry failed', ['ref' => $refId, 'error' => $e->getMessage()]);
            $results['errors'][] = "Entry {$refId}: {$e->getMessage()}";
            $results['conflicts']++;
        }
    }

    private function syncRawPayload(Company $company, array $payload, array &$results): void
    {
        $txnId = $payload['transaction_id'] ?? null;
        if (! $txnId) {
            $results['skipped']++;
            return;
        }

        $exists = RawPayloadQueue::where('transaction_id', $txnId)->exists();
        if ($exists) {
            $results['skipped']++;
            return;
        }

        RawPayloadQueue::create([
            'operator'       => $payload['operator'] ?? 'MTN',
            'transaction_id' => $txnId,
            'company_niu'    => $company->niu,
            'amount'         => $payload['amount'] ?? '0',
            'message'        => $payload['message'] ?? '',
            'txn_date'       => $payload['date'] ?? now()->toDateString(),
            'status'         => 'QUEUED',
        ]);

        \App\Jobs\ProcessTelecomPayload::dispatch($txnId);
        $results['applied']++;
    }

    private function serializeEntry(JournalEntry $entry): array
    {
        return [
            'id'                  => $entry->id,
            'reference_id'        => $entry->reference_id,
            'posting_date'        => $entry->posting_date,
            'posting_type'        => $entry->posting_type,
            'source_pipeline'     => $entry->source_pipeline,
            'transaction_status'  => $entry->transaction_status,
            'memo'                => $entry->memo,
            'invoice_crypto_hash' => $entry->invoice_crypto_hash,
            'updated_at'          => $entry->updated_at->toIso8601String(),
            'lines'               => $entry->lines->map(fn ($l) => [
                'account_code' => $l->account->code,
                'debit'        => $l->debit,
                'credit'       => $l->credit,
            ])->toArray(),
        ];
    }
}
