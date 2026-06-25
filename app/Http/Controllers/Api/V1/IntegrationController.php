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
