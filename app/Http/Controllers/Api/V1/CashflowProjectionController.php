<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashflowProjectionController extends Controller
{
    // GET /companies/{company}/cashflow/projection
    // Returns forward-looking 30/60/90-day cash inflows + outflows from open invoices/payables
    public function projection(Request $request, Company $company)
    {
        $today  = now()->toDateString();
        $d30    = now()->addDays(30)->toDateString();
        $d60    = now()->addDays(60)->toDateString();
        $d90    = now()->addDays(90)->toDateString();

        // Receivables: open customer invoices (SENT, PARTIAL)
        $receivables = DB::select("
            SELECT ci.invoice_number, ci.due_date, ci.amount_ttc,
                   COALESCE(ci.withholding_received, 0) AS withholding_received,
                   COALESCE(ci.net_receivable, ci.amount_ttc) AS net_receivable,
                   c.name AS counterparty
            FROM customer_invoices ci
            JOIN customers c ON ci.customer_id = c.id
            WHERE ci.company_id = ?
              AND ci.status IN ('SENT', 'PARTIAL')
              AND ci.due_date >= ?
              AND ci.due_date <= ?
              AND ci.deleted_at IS NULL
            ORDER BY ci.due_date
        ", [$company->id, $today, $d90]);

        // Payables: open supplier invoices (not PAID)
        $payables = DB::select("
            SELECT si.invoice_number, si.due_date, si.amount_ttc AS net_payable,
                   s.name AS counterparty
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            WHERE si.company_id = ?
              AND si.status NOT IN ('PAID', 'CANCELLED')
              AND si.due_date >= ?
              AND si.due_date <= ?
              AND si.deleted_at IS NULL
            ORDER BY si.due_date
        ", [$company->id, $today, $d90]);

        $buckets = [
            '0-30'  => ['inflow' => 0, 'outflow' => 0],
            '31-60' => ['inflow' => 0, 'outflow' => 0],
            '61-90' => ['inflow' => 0, 'outflow' => 0],
        ];

        foreach ($receivables as $r) {
            $key = $this->bucket($r->due_date, $today, $d30, $d60);
            $buckets[$key]['inflow'] += (float) $r->net_receivable;
        }

        foreach ($payables as $p) {
            $key = $this->bucket($p->due_date, $today, $d30, $d60);
            $buckets[$key]['outflow'] += (float) $p->net_payable;
        }

        foreach ($buckets as &$b) {
            $b['net'] = round($b['inflow'] - $b['outflow'], 2);
        }

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'currency'     => 'XAF',
            'buckets'      => $buckets,
            'receivables'  => $receivables,
            'payables'     => $payables,
            'summary'      => [
                'total_inflow'  => round(array_sum(array_column($buckets, 'inflow')), 2),
                'total_outflow' => round(array_sum(array_column($buckets, 'outflow')), 2),
                'net_position'  => round(array_sum(array_column($buckets, 'net')), 2),
            ],
        ]);
    }

    private function bucket(string $dueDate, string $today, string $d30, string $d60): string
    {
        if ($dueDate <= $d30) return '0-30';
        if ($dueDate <= $d60) return '31-60';
        return '61-90';
    }
}
