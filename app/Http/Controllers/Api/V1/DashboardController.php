<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\JournalEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Aggregated data for the tenant dashboard: 6-month revenue/expense trend,
 * recent activity, A/R aging, overdue invoices and cash position.
 * Grouping is done in PHP so it works on both SQLite (dev) and MySQL (prod).
 */
class DashboardController extends Controller
{
    public function summary(Company $company): JsonResponse
    {
        $now   = Carbon::now()->startOfMonth();
        $start = (clone $now)->subMonths(5);

        // --- Build the last 6 month buckets ---
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $m = (clone $start)->addMonths($i);
            $months[$m->format('Y-m')] = ['label' => $m->isoFormat('MMM'), 'revenue' => 0.0, 'expense' => 0.0];
        }

        // --- Pull posted lines in range with account class ---
        $rows = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('syscohada_accounts as sa', 'sa.id', '=', 'jl.syscohada_account_id')
            ->where('je.company_id', $company->id)
            ->where('je.transaction_status', 'SUCCESSFUL')
            ->whereDate('je.posting_date', '>=', $start->toDateString())
            ->select('je.posting_date', 'sa.code', 'jl.debit', 'jl.credit')
            ->get();

        foreach ($rows as $r) {
            $key = Carbon::parse($r->posting_date)->format('Y-m');
            if (! isset($months[$key])) continue;
            $class = substr((string) $r->code, 0, 1);
            if ($class === '7') {           // revenue = credit - debit
                $months[$key]['revenue'] += (float) $r->credit - (float) $r->debit;
            } elseif ($class === '6') {      // expense = debit - credit
                $months[$key]['expense'] += (float) $r->debit - (float) $r->credit;
            }
        }

        $series = collect($months)->map(fn ($m) => [
            'label'   => $m['label'],
            'revenue' => round($m['revenue'], 0),
            'expense' => round($m['expense'], 0),
        ])->values();

        // --- Cash position: net balance of class 5 (treasury) accounts ---
        $cash = (float) DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('syscohada_accounts as sa', 'sa.id', '=', 'jl.syscohada_account_id')
            ->where('je.company_id', $company->id)
            ->where('je.transaction_status', 'SUCCESSFUL')
            ->where('sa.code', 'like', '5%')
            ->sum(DB::raw('jl.debit - jl.credit'));

        // --- Recent activity (last 8 entries) ---
        $recent = JournalEntry::where('company_id', $company->id)
            ->orderByDesc('posting_date')->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'posting_date', 'reference_id', 'memo', 'posting_type'])
            ->map(function ($e) {
                $amount = (float) DB::table('journal_lines')->where('journal_entry_id', $e->id)->sum('debit');
                return [
                    'date'   => optional($e->posting_date)->format('Y-m-d'),
                    'ref'    => $e->reference_id,
                    'memo'   => $e->memo,
                    'type'   => $e->posting_type,
                    'amount' => round($amount, 0),
                ];
            });

        // --- A/R aging + overdue (outstanding customer invoices) ---
        $today      = Carbon::now();
        $outstanding = CustomerInvoice::where('company_id', $company->id)
            ->whereIn('status', ['SENT', 'OVERDUE'])
            ->get(['amount_ttc', 'due_date', 'status']);

        $aging = ['current' => 0.0, 'd30' => 0.0, 'd60' => 0.0, 'd90' => 0.0];
        $overdueCount = 0; $overdueAmount = 0.0;
        foreach ($outstanding as $inv) {
            $amt = (float) $inv->amount_ttc;
            $days = $inv->due_date ? $today->diffInDays(Carbon::parse($inv->due_date), false) : 0;
            if ($days >= 0)        $aging['current'] += $amt;
            elseif ($days >= -30)  $aging['d30'] += $amt;
            elseif ($days >= -60)  $aging['d60'] += $amt;
            else                   $aging['d90'] += $amt;
            if ($days < 0) { $overdueCount++; $overdueAmount += $amt; }
        }

        return response()->json([
            'series'   => $series,
            'cash'     => round($cash, 0),
            'recent'   => $recent,
            'aging'    => array_map(fn ($v) => round($v, 0), $aging),
            'ar_total' => round(array_sum($aging), 0),
            'overdue'  => ['count' => $overdueCount, 'amount' => round($overdueAmount, 0)],
        ]);
    }
}
