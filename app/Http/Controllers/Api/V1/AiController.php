<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AiConfig;
use App\Models\CustomerInvoice;
use App\Models\JournalEntry;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    private function service(Request $request): AiService
    {
        $config = AiConfig::where('company_id', $request->user()->company_id)->first();
        return new AiService($config);
    }

    private function log(Request $request, string $feature, array $input, array $result): void
    {
        \App\Models\AiSuggestion::create([
            'company_id'       => $request->user()->company_id,
            'user_id'          => $request->user()->id,
            'feature'          => $feature,
            'input_data'       => $input,
            'suggestion'       => $result['data'] ?? ($result['ok'] ? $result : null),
            'model_used'       => $result['model'] ?? null,
            'was_online'       => ($result['mode'] ?? '') === 'online',
            'response_time_ms' => $result['response_time_ms'] ?? 0,
            'created_at'       => now(),
        ]);
    }

    /** GET /api/v1/ai/status */
    public function status(Request $request): JsonResponse
    {
        return response()->json($this->service($request)->status());
    }

    /** GET /api/v1/ai/config */
    public function getConfig(Request $request): JsonResponse
    {
        $c = AiConfig::firstOrNew(['company_id' => $request->user()->company_id]);
        return response()->json([
            'has_gemini_key'  => ! empty($c->gemini_api_key),
            'ollama_enabled'  => (bool) ($c->ollama_enabled ?? false),
            'ollama_model'    => $c->ollama_model ?? config('ai.ollama_model'),
            'auto_categorize' => (bool) ($c->auto_categorize ?? false),
            'auto_dsf_check'  => (bool) ($c->auto_dsf_check ?? false),
            'anomaly_scan'    => (bool) ($c->anomaly_scan ?? false),
        ]);
    }

    /** PUT /api/v1/ai/config (OWNER only) */
    public function saveConfig(Request $request): JsonResponse
    {
        if ($request->user()->role !== 'OWNER') {
            abort(403, 'Only the owner may change AI settings.');
        }
        $data = $request->validate([
            'gemini_api_key'  => 'nullable|string|max:200',
            'ollama_enabled'  => 'boolean',
            'ollama_model'    => 'nullable|string|max:60',
            'auto_categorize' => 'boolean',
            'auto_dsf_check'  => 'boolean',
            'anomaly_scan'    => 'boolean',
        ]);

        $c = AiConfig::firstOrNew(['company_id' => $request->user()->company_id]);
        // Only overwrite the key when a new value is provided (blank = keep existing).
        if (array_key_exists('gemini_api_key', $data) && $data['gemini_api_key'] !== null && $data['gemini_api_key'] !== '') {
            $c->gemini_api_key = $data['gemini_api_key'];
        }
        $c->fill(collect($data)->except('gemini_api_key')->toArray());
        $c->company_id = $request->user()->company_id;
        $c->save();

        return response()->json(['ok' => true, 'status' => $this->service($request)->status()]);
    }

    /** POST /api/v1/ai/categorize */
    public function categorize(Request $request): JsonResponse
    {
        $data = $request->validate([
            'description' => 'required|string|max:500',
            'amount'      => 'required|numeric',
        ]);
        $result = $this->service($request)->categorize($data['description'], (float) $data['amount']);
        $this->log($request, 'categorize', $data, $result);
        return response()->json($result);
    }

    /** POST /api/v1/ai/dsf-check */
    public function dsfCheck(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;
        $entries = JournalEntry::where('company_id', $cid)->latest('id')->limit(50)
            ->get(['id', 'reference', 'description', 'posting_date'])->toArray();
        $result = $this->service($request)->checkDsf($entries);
        $this->log($request, 'dsf_check', ['count' => count($entries)], $result);
        return response()->json($result);
    }

    /** POST /api/v1/ai/anomalies */
    public function anomalies(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;
        $entries = JournalEntry::where('company_id', $cid)->latest('id')->limit(80)
            ->get(['id', 'reference', 'description', 'posting_date'])->toArray();
        $result = $this->service($request)->detectAnomalies($entries);
        $this->log($request, 'anomaly', ['count' => count($entries)], $result);
        return response()->json($result);
    }

    /** POST /api/v1/ai/query — natural-language question over the company's own data */
    public function query(Request $request): JsonResponse
    {
        $data = $request->validate(['question' => 'required|string|max:500']);
        $cid  = $request->user()->company_id;

        // Real data context (never fabricated by the model).
        $context = [
            'company'           => $request->user()->company?->name,
            'currency'          => 'XAF',
            'invoices_total'    => round((float) CustomerInvoice::where('company_id', $cid)->sum('amount_ttc'), 2),
            'tva_collected'     => round((float) CustomerInvoice::where('company_id', $cid)->sum('tva_amount'), 2),
            'invoice_count'     => CustomerInvoice::where('company_id', $cid)->count(),
            'revenue_this_month'=> round((float) CustomerInvoice::where('company_id', $cid)
                                    ->whereMonth('invoice_date', now()->month)->sum('amount_ht'), 2),
            'journal_entries'   => JournalEntry::where('company_id', $cid)->count(),
        ];

        $result = $this->service($request)->naturalQuery($data['question'], $context);
        $this->log($request, 'query', $data, $result);
        return response()->json($result);
    }
}
