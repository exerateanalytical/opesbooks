<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CameroonTaxEngine;
use App\Services\JournalPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManualJournalController extends Controller
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * POST /api/v1/journal/manual
     *
     * Accepts a fully structured manual journal entry with explicit debit/credit lines.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_niu'      => 'required|string',
            'posting_date'     => 'required|date_format:Y-m-d',
            'reference_id'     => 'required|string|max:100|unique:journal_entries,reference_id',
            'source_pipeline'  => ['required', Rule::in(['MANUAL_CASH', 'MANUAL_BANK', 'MANUAL_INVOICE'])],
            'memo'             => 'required|string|max:500',
            'lines'            => 'required|array|min:2',
            'lines.*.account_code' => 'required|string|exists:syscohada_accounts,code',
            'lines.*.debit'        => 'required|numeric|min:0',
            'lines.*.credit'       => 'required|numeric|min:0',
            'lines.*.description'  => 'nullable|string|max:255',
        ]);

        $company = Company::where('niu', $data['company_niu'])->firstOrFail();

        if (! $company->hasValidFiscalProfile()) {
            throw ValidationException::withMessages([
                'company_niu' => ['Company fiscal profile is incomplete (NIU, RCCM, or Tax Center missing).'],
            ]);
        }

        $lines = array_map(fn ($l) => [
            'account_code' => $l['account_code'],
            'debit'        => (string) $l['debit'],
            'credit'       => (string) $l['credit'],
            'description'  => $l['description'] ?? null,
        ], $data['lines']);

        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => $data['posting_date'],
            'reference_id'    => $data['reference_id'],
            'source_pipeline' => $data['source_pipeline'],
            'memo'            => $data['memo'],
            'status'          => 'POSTED',
        ], $lines);

        return response()->json([
            'message'       => 'Manual journal entry posted successfully.',
            'journal_entry' => $entry,
        ], 201);
    }
}
