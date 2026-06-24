<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTelecomPayload;
use App\Models\RawPayloadQueue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelecomCallbackController extends Controller
{
    /**
     * POST /api/v1/ingest/telecom/callback
     *
     * Immediately acknowledges receipt (HTTP 202) and queues the payload
     * for asynchronous processing on a background worker thread.
     * This prevents server bottleneck under high-velocity MoMo transaction bursts.
     */
    public function handle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_niu'    => 'required|string|max:30',
            'operator'       => 'required|in:MTN,ORANGE',
            'transaction_id' => 'required|string|max:100',
            'amount'         => 'required|numeric|min:1',
            'message'        => 'required|string|max:500',
            'date'           => 'nullable|date_format:Y-m-d',
        ]);

        // Idempotency guard — duplicate transaction IDs are silently acknowledged
        if (RawPayloadQueue::where('transaction_id', $data['transaction_id'])->exists()) {
            return response()->json([
                'status'  => 'DUPLICATE',
                'message' => 'Transaction already received and queued.',
            ], 202);
        }

        $raw = RawPayloadQueue::create([
            'operator'       => $data['operator'],
            'transaction_id' => $data['transaction_id'],
            'company_niu'    => $data['company_niu'],
            'amount'         => (string) $data['amount'],
            'message'        => $data['message'],
            'txn_date'       => $data['date'] ?? now()->toDateString(),
            'status'         => 'QUEUED',
        ]);

        ProcessTelecomPayload::dispatch($raw->id);

        return response()->json([
            'status'   => 'QUEUED',
            'queue_id' => $raw->id,
            'message'  => 'Payload received and queued for processing.',
        ], 202);
    }
}
