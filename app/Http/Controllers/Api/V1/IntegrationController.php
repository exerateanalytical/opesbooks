<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomerInvoice;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

/**
 * Public "API-as-a-product" surface for third-party integrators.
 * All endpoints are authenticated by the `apikey` middleware, which sets
 * the tenant scope via app('current_api_company_id').
 */
class IntegrationController extends Controller
{
    private function companyId(): int
    {
        return (int) app('current_api_company_id');
    }

    /** GET /api/v1/integration/invoices */
    public function invoices(Request $request)
    {
        $invoices = CustomerInvoice::where('company_id', $this->companyId())
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('invoice_date')
            ->paginate(min((int) $request->per_page ?: 25, 100));

        return response()->json($invoices);
    }

    /** GET /api/v1/integration/invoices/{id} */
    public function showInvoice(int $id)
    {
        $invoice = CustomerInvoice::where('company_id', $this->companyId())
            ->with('customer')
            ->findOrFail($id);

        return response()->json($invoice);
    }

    /** POST /api/v1/integration/invoices — create a customer invoice. */
    public function storeInvoice(Request $request)
    {
        $cid = $this->companyId();
        $company = \App\Models\Company::find($cid);

        if (! app(\App\Services\PlanLimitService::class)->canCreateInvoice($company)) {
            return response()->json(app(\App\Services\PlanLimitService::class)->limitReached($company, 'factures/mois'), 402);
        }

        $data = $request->validate([
            'client_id'          => 'required|integer',
            'invoice_date'       => 'required|date',
            'due_date'           => 'nullable|date|after_or_equal:invoice_date',
            'items'              => 'required|array|min:1',
            'items.*.description'=> 'required|string|max:255',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
        ]);

        // Client must belong to the API key's company.
        $customer = \App\Models\Customer::where('company_id', $cid)->find($data['client_id']);
        if (! $customer) {
            return response()->json(['error' => 'invalid_client', 'message' => 'client_id not found for this company.'], 422);
        }

        $amountHt = collect($data['items'])->sum(fn ($i) => $i['quantity'] * $i['unit_price']);
        $tax = \App\Services\CameroonTaxEngine::compute((string) $amountHt);

        $invoice = \App\Models\CustomerInvoice::create([
            'company_id'     => $cid,
            'customer_id'    => $customer->id,
            'invoice_number' => 'API-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
            'invoice_date'   => $data['invoice_date'],
            'due_date'       => $data['due_date'] ?? \Illuminate\Support\Carbon::parse($data['invoice_date'])->addDays(30),
            'amount_ht'      => $tax['amount_ht'],
            'tva_amount'     => $tax['base_vat'],
            'cac_amount'     => $tax['cac'],
            'amount_ttc'     => $tax['amount_ttc'],
            'status'         => 'DRAFT',
            'notes'          => $data['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $invoice->load('customer')], 201);
    }

    /** POST /api/v1/integration/invoices/{id}/void */
    public function voidInvoice(Request $request, int $id)
    {
        $invoice = CustomerInvoice::where('company_id', $this->companyId())->findOrFail($id);
        if ($invoice->status === 'PAID') {
            return response()->json(['error' => 'cannot_void_paid', 'message' => 'Cannot void a paid invoice. Issue a credit note instead.'], 422);
        }
        $invoice->update(['status' => 'CANCELLED']);
        return response()->json(['success' => true, 'data' => $invoice]);
    }

    /** GET /api/v1/integration/journal */
    public function journal(Request $request)
    {
        $entries = JournalEntry::where('company_id', $this->companyId())
            ->latest('id')
            ->paginate(min((int) $request->per_page ?: 25, 100));

        return response()->json($entries);
    }

    /** GET /api/v1/integration/tax/vat-summary */
    public function vatSummary(Request $request)
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to   = $request->date('to')   ?? now()->endOfMonth();

        $q = CustomerInvoice::where('company_id', $this->companyId())
            ->whereBetween('invoice_date', [$from, $to]);

        return response()->json([
            'period'      => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'amount_ht'   => round((float) (clone $q)->sum('amount_ht'), 2),
            'tva_collected' => round((float) (clone $q)->sum('tva_amount'), 2),
            'cac'         => round((float) (clone $q)->sum('cac_amount'), 2),
            'amount_ttc'  => round((float) (clone $q)->sum('amount_ttc'), 2),
            'invoice_count' => (clone $q)->count(),
        ]);
    }

    /** GET /api/v1/integration/webhooks */
    public function webhooks(Request $request)
    {
        return response()->json(
            \App\Models\WebhookEndpoint::where('company_id', $this->companyId())->get()
        );
    }

    /** POST /api/v1/integration/webhooks */
    public function storeWebhook(Request $request)
    {
        $data = $request->validate([
            'url'      => 'required|url|max:500',
            'events'   => 'required|array|min:1',
            'events.*' => 'in:' . implode(',', \App\Models\WebhookEndpoint::EVENTS),
        ]);

        $secret = \Illuminate\Support\Str::random(32);
        $endpoint = \App\Models\WebhookEndpoint::create([
            'company_id' => $this->companyId(),
            'api_key_id' => $request->attributes->get('api_key')?->id,
            'url'        => $data['url'],
            'events'     => $data['events'],
            'secret'     => $secret,
        ]);

        // Secret returned ONCE (it is hidden on subsequent reads).
        return response()->json(array_merge($endpoint->toArray(), ['secret' => $secret]), 201);
    }

    /** DELETE /api/v1/integration/webhooks/{id} */
    public function destroyWebhook(Request $request, int $id)
    {
        $endpoint = \App\Models\WebhookEndpoint::where('company_id', $this->companyId())->findOrFail($id);
        $endpoint->delete();
        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/integration/webhooks/{id}/test */
    public function testWebhook(Request $request, int $id)
    {
        $endpoint = \App\Models\WebhookEndpoint::where('company_id', $this->companyId())->findOrFail($id);
        app(\App\Services\WebhookService::class)->dispatch('webhook.test', ['message' => 'Test event from OPESBooks'], $endpoint->company);
        return response()->json(['ok' => true, 'message' => 'Test event queued for delivery.']);
    }

    /** GET /api/v1/integration/reports/pl */
    public function profitAndLoss(Request $request)
    {
        $cid  = $this->companyId();
        $from = $request->date('from') ?? now()->startOfYear();
        $to   = $request->date('to')   ?? now()->endOfYear();

        $revenue = (float) CustomerInvoice::where('company_id', $cid)
            ->whereBetween('invoice_date', [$from, $to])->sum('amount_ht');

        return response()->json([
            'period'    => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'revenue_ht' => round($revenue, 2),
            'currency'  => 'XAF',
            'note'      => 'Summary view — full P&L available via the journal endpoint.',
        ]);
    }
}
