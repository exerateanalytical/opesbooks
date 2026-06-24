<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\RawPayloadQueue;
use App\Services\MomoIngestionService;
use App\Services\TelecomReversalService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessTelecomPayload implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public readonly int $rawQueueId) {}

    public function handle(MomoIngestionService $ingestion, TelecomReversalService $reversal): void
    {
        $raw = RawPayloadQueue::findOrFail($this->rawQueueId);

        if ($raw->status !== 'QUEUED') {
            return; // Already processed (duplicate dispatch guard)
        }

        $raw->update(['status' => 'PROCESSING']);

        try {
            $company = Company::where('niu', $raw->company_niu)->firstOrFail();

            // Check if this is a reversal/cancellation signal
            if ($reversal->isReversalPayload($raw->message)) {
                $original = JournalEntry::where('reference_id', $raw->transaction_id)->first();

                if ($original) {
                    $reversal->reverse($original, $raw->message);
                }
            } else {
                $ingestion->ingest($company, [
                    'operator'       => $raw->operator,
                    'amount'         => (string) $raw->amount,
                    'transaction_id' => $raw->transaction_id,
                    'message'        => $raw->message,
                    'date'           => $raw->txn_date->toDateString(),
                ]);
            }

            $raw->update(['status' => 'DONE']);
        } catch (Throwable $e) {
            $raw->update([
                'status'       => 'FAILED',
                'error_detail' => $e->getMessage(),
            ]);
            throw $e; // Allows Laravel queue to retry
        }
    }

    public function failed(Throwable $e): void
    {
        RawPayloadQueue::where('id', $this->rawQueueId)
            ->update(['status' => 'FAILED', 'error_detail' => $e->getMessage()]);
    }
}
