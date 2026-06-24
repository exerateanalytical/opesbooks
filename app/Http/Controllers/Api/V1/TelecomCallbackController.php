<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\MomoIngestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TelecomCallbackController extends Controller
{
    public function __construct(private MomoIngestionService $ingestion) {}

    public function handle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_niu'    => 'required|string',
            'operator'       => 'required|in:MTN,ORANGE',
            'transaction_id' => 'required|string|max:100',
            'amount'         => 'required|numeric|min:1',
            'message'        => 'required|string|max:500',
            'date'           => 'nullable|date_format:Y-m-d',
        ]);

        $company = Company::where('niu', $data['company_niu'])->firstOrFail();

        if (! $company->hasValidFiscalProfile()) {
            throw ValidationException::withMessages([
                'company_niu' => ['Company fiscal profile is incomplete (NIU, RCCM, or Tax Center missing).'],
            ]);
        }

        $entry = $this->ingestion->ingest($company, [
            'operator'       => $data['operator'],
            'amount'         => (string) $data['amount'],
            'transaction_id' => $data['transaction_id'],
            'message'        => $data['message'],
            'date'           => $data['date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'message'       => 'Transaction ingested and posted to ledger.',
            'journal_entry' => $entry,
        ], 201);
    }
}
