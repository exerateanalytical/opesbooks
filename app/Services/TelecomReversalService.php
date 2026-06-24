<?php

namespace App\Services;

use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Telecom Reversal Interceptor.
 *
 * When MTN MoMo or Orange Money sends a cancellation or rollback signal,
 * this service:
 *   1. Marks the original entry as REVERSED (never deletes).
 *   2. Posts a perfectly mirrored counter-entry — every debit becomes a
 *      credit and vice-versa — preserving ledger integrity without
 *      erasing audit history.
 */
class TelecomReversalService
{
    public function __construct(private JournalPostingService $poster) {}

    public function reverse(JournalEntry $entry, string $reason = 'Operator cancellation'): JournalEntry
    {
        if ($entry->transaction_status === 'REVERSED') {
            throw ValidationException::withMessages([
                'journal_entry' => ['This entry has already been reversed.'],
            ]);
        }

        if ($entry->posting_type === 'ADJUSTMENT') {
            throw ValidationException::withMessages([
                'journal_entry' => ['Adjustment entries cannot be reversed programmatically.'],
            ]);
        }

        return DB::transaction(function () use ($entry, $reason) {
            // Stamp original entry as REVERSED (immutable audit trail)
            $entry->update(['transaction_status' => 'REVERSED']);

            // Load lines if not eager-loaded
            $entry->loadMissing('lines.account');

            // Mirror lines: flip debit/credit
            $reversalLines = $entry->lines->map(fn ($line) => [
                'account_code' => $line->account->code,
                'debit'        => (string) $line->credit,
                'credit'       => (string) $line->debit,
                'description'  => "REVERSAL: {$line->description}",
            ])->toArray();

            $reversalRef = 'REV-' . $entry->reference_id . '-' . Str::random(6);

            return $this->poster->post([
                'company_id'         => $entry->company_id,
                'user_id'            => $entry->user_id,
                'posting_date'       => now()->toDateString(),
                'posting_type'       => 'STANDARD',
                'reference_id'       => $reversalRef,
                'source_pipeline'    => $entry->source_pipeline,
                'transaction_status' => 'SUCCESSFUL',
                'memo'               => "REVERSAL of {$entry->reference_id}: {$reason}",
            ], $reversalLines);
        });
    }

    /** Detect if an incoming MoMo payload string signals a cancellation. */
    public function isReversalPayload(string $message): bool
    {
        return (bool) preg_match(
            '/annul[eé]|revers[eé]|rembours|cancel|rollback|échec|failed|refund/i',
            $message
        );
    }
}
