<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\OfflineSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineSyncController extends Controller
{
    public function __construct(private OfflineSyncService $sync) {}

    /** Resolve the company by NIU and verify the caller belongs to it. */
    private function resolveOwnedCompany(Request $request, string $niu): Company
    {
        $company = Company::where('niu', $niu)->firstOrFail();
        abort_unless(
            (int) $request->user()->company_id === (int) $company->id || $request->user()->belongsToCompany($company->id),
            403,
            'You do not have access to this company.'
        );
        return $company;
    }

    /**
     * POST /api/v1/sync/push
     * Receives an offline bundle from the client and merges it into MySQL.
     */
    public function push(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id'                              => 'required|string|max:64',
            'company_niu'                            => 'required|string',
            'synced_at'                              => 'required|date',
            'journal_entries'                        => 'nullable|array',
            'journal_entries.*.reference_id'         => 'required|string',
            'journal_entries.*.posting_date'         => 'required|date_format:Y-m-d',
            'journal_entries.*.memo'                 => 'required|string',
            'journal_entries.*.lines'                => 'required|array|min:2',
            'journal_entries.*.lines.*.account_code' => 'required|string',
            'journal_entries.*.lines.*.debit'        => 'required|numeric|min:0',
            'journal_entries.*.lines.*.credit'       => 'required|numeric|min:0',
            'raw_payloads'                           => 'nullable|array',
        ]);

        $company = $this->resolveOwnedCompany($request, $data['company_niu']);
        $result  = $this->sync->processBundle($company, $data);

        return response()->json([
            'message' => 'Sync bundle processed.',
            'result'  => $result,
        ]);
    }

    /**
     * GET /api/v1/sync/pull?since=2026-06-01T00:00:00Z&company_niu=CM12345
     * Returns delta of all entries changed since the checkpoint.
     */
    public function pull(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_niu' => 'required|string',
            'since'       => 'required|date',
        ]);

        $company = $this->resolveOwnedCompany($request, $data['company_niu']);
        $delta   = $this->sync->pullDelta($company, $data['since']);

        return response()->json($delta);
    }

    /**
     * GET /api/v1/sync/status?company_niu=CM12345
     * Lightweight heartbeat for the client to check if it needs to push/pull.
     */
    public function status(Request $request): JsonResponse
    {
        $request->validate(['company_niu' => 'required|string']);
        $company = $this->resolveOwnedCompany($request, $request->company_niu);

        return response()->json($this->sync->status($company));
    }
}
