<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerInvoice;
use App\Models\MecefConfig;
use App\Services\MecefService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MecefController extends Controller
{
    /** GET /api/v1/mecef/config */
    public function getConfig(Request $request): JsonResponse
    {
        $c = MecefConfig::firstOrNew(['company_id' => $request->user()->company_id]);
        return response()->json([
            'nim'          => $c->nim,
            'api_endpoint' => $c->api_endpoint,
            'has_token'    => ! empty($c->api_token),
            'is_active'    => (bool) ($c->is_active ?? false),
            'sandbox_mode' => (bool) ($c->sandbox_mode ?? true),
        ]);
    }

    /** PUT /api/v1/mecef/config (OWNER only) */
    public function saveConfig(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'OWNER') {
            abort(403, 'Only the owner may change MECeF settings.');
        }
        $data = $request->validate([
            'nim'          => 'nullable|string|max:100',
            'api_endpoint' => 'nullable|url|max:255',
            'api_token'    => 'nullable|string|max:255',
            'is_active'    => 'boolean',
            'sandbox_mode' => 'boolean',
        ]);

        $c = MecefConfig::firstOrNew(['company_id' => $request->user()->company_id]);
        $c->nim          = $data['nim'] ?? $c->nim;
        $c->api_endpoint = $data['api_endpoint'] ?? $c->api_endpoint;
        if (! empty($data['api_token'])) {
            $c->api_token = $data['api_token'];
        }
        $c->is_active    = $data['is_active'] ?? false;
        $c->sandbox_mode = $data['sandbox_mode'] ?? true;
        $c->company_id   = $request->user()->company_id;
        $c->save();

        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/mecef/invoices/{invoice}/certify */
    public function certify(Request $request, int $invoice): JsonResponse
    {
        $inv = CustomerInvoice::where('company_id', $request->user()->company_id)->findOrFail($invoice);
        $config = MecefConfig::where('company_id', $request->user()->company_id)->first();

        $result = (new MecefService($config))->certifyInvoice($inv);

        return response()->json([
            'ok'      => $result['success'] ?? false,
            'message' => $result['message'] ?? null,
            'invoice' => $inv->fresh()->only(['id', 'mecef_status', 'mecef_counter', 'mecef_nim', 'mecef_qr_data', 'mecef_certified_at']),
        ], ($result['success'] ?? false) ? 200 : 422);
    }

    /** GET /api/v1/mecef/stats */
    public function stats(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;
        $base = CustomerInvoice::where('company_id', $cid);

        return response()->json([
            'certified_total'  => (clone $base)->where('mecef_status', 'certified')->count(),
            'certified_month'  => (clone $base)->where('mecef_status', 'certified')
                                    ->whereMonth('mecef_certified_at', now()->month)->count(),
            'certified_value'  => round((float) (clone $base)->where('mecef_status', 'certified')->sum('amount_ttc'), 2),
            'failed'           => (clone $base)->where('mecef_status', 'failed')->count(),
            'pending'          => (clone $base)->whereIn('mecef_status', ['not_submitted', 'pending'])->count(),
        ]);
    }
}
