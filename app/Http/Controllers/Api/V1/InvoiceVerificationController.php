<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Services\CryptographicInvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceVerificationController extends Controller
{
    public function __construct(private CryptographicInvoiceService $crypto) {}

    /**
     * GET /api/v1/verify/invoice/{hash}
     *
     * Public DGI verification endpoint — returns the matching journal entry
     * data for a given SHA-256 invoice fingerprint. Safe to expose publicly
     * as the hash is non-reversible.
     */
    public function verify(string $hash): JsonResponse
    {
        $entry = JournalEntry::with('company', 'lines.account')
            ->where('invoice_crypto_hash', $hash)
            ->first();

        if (! $entry) {
            return response()->json([
                'verified'  => false,
                'message'   => 'No matching invoice found for this hash.',
                'hash'      => $hash,
            ], 404);
        }

        return response()->json([
            'verified'       => true,
            'hash'           => $hash,
            'company_niu'    => $entry->company->niu,
            'company_name'   => $entry->company->name,
            'reference_id'   => $entry->reference_id,
            'posting_date'   => $entry->posting_date->toDateString(),
            'total_debit'    => number_format($entry->lines->sum('debit'), 2, '.', ''),
            'total_credit'   => number_format($entry->lines->sum('credit'), 2, '.', ''),
            'verified_at'    => now()->toIso8601String(),
        ]);
    }
}
