<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\SyscohadaAccount;
use Brick\Math\BigDecimal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function entries(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'from'     => 'nullable|date_format:Y-m-d',
            'to'       => 'nullable|date_format:Y-m-d|after_or_equal:from',
            'pipeline' => 'nullable|in:AUTOMATED_MOMO,AUTOMATED_ORANGE,MANUAL_CASH,MANUAL_BANK,MANUAL_INVOICE',
            'per_page' => 'nullable|integer|min:5|max:200',
        ]);

        $query = JournalEntry::with('lines.account')
            ->where('company_id', $company->id)
            ->orderByDesc('posting_date');

        if ($request->filled('from')) {
            $query->whereDate('posting_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('posting_date', '<=', $request->input('to'));
        }
        if ($request->filled('pipeline')) {
            $query->where('source_pipeline', $request->input('pipeline'));
        }

        return response()->json($query->paginate($request->input('per_page', 30)));
    }

    public function trialBalance(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'from' => 'nullable|date_format:Y-m-d',
            'to'   => 'nullable|date_format:Y-m-d',
        ]);

        $rows = SyscohadaAccount::select(
                'syscohada_accounts.id',
                'syscohada_accounts.code',
                'syscohada_accounts.label',
                'syscohada_accounts.class_digit'
            )
            ->selectRaw('COALESCE(SUM(journal_lines.debit), 0) AS total_debit')
            ->selectRaw('COALESCE(SUM(journal_lines.credit), 0) AS total_credit')
            ->leftJoin('journal_lines', 'journal_lines.syscohada_account_id', '=', 'syscohada_accounts.id')
            ->leftJoin('journal_entries', function ($join) use ($company, $request) {
                $join->on('journal_entries.id', '=', 'journal_lines.journal_entry_id')
                    ->where('journal_entries.company_id', $company->id)
                    ->where('journal_entries.transaction_status', 'SUCCESSFUL');

                if ($request->filled('from')) {
                    $join->whereDate('journal_entries.posting_date', '>=', $request->input('from'));
                }
                if ($request->filled('to')) {
                    $join->whereDate('journal_entries.posting_date', '<=', $request->input('to'));
                }
            })
            ->groupBy('syscohada_accounts.id', 'syscohada_accounts.code', 'syscohada_accounts.label', 'syscohada_accounts.class_digit')
            ->orderBy('syscohada_accounts.code')
            ->get();

        $grandDebit  = $rows->sum('total_debit');
        $grandCredit = $rows->sum('total_credit');

        return response()->json([
            'company_id'   => $company->id,
            'company_name' => $company->name,
            'balanced'     => BigDecimal::of((string) $grandDebit)->isEqualTo(BigDecimal::of((string) $grandCredit)),
            'grand_debit'  => number_format($grandDebit, 2, '.', ''),
            'grand_credit' => number_format($grandCredit, 2, '.', ''),
            'accounts'     => $rows,
        ]);
    }

    /** GET /trial-balance/pdf — printable Balance des comptes (SYSCOHADA). */
    public function trialBalancePdf(Request $request, Company $company)
    {
        $data = $this->trialBalance($request, $company)->getData(true);
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.trial_balance', [
            'company' => $company,
            'data'    => $data,
            'from'    => $request->input('from'),
            'to'      => $request->input('to'),
        ])->setPaper('a4');

        return $pdf->stream('balance-des-comptes.pdf');
    }

    /** GET /grand-livre/pdf — printable General Ledger grouped by account. */
    public function grandLivrePdf(Request $request, Company $company)
    {
        $rows = \Illuminate\Support\Facades\DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('syscohada_accounts as sa', 'sa.id', '=', 'jl.syscohada_account_id')
            ->where('je.company_id', $company->id)
            ->where('je.transaction_status', 'SUCCESSFUL')
            ->when($request->input('from'), fn ($q, $v) => $q->whereDate('je.posting_date', '>=', $v))
            ->when($request->input('to'),   fn ($q, $v) => $q->whereDate('je.posting_date', '<=', $v))
            ->orderBy('sa.code')->orderBy('je.posting_date')
            ->get(['sa.code', 'sa.label', 'je.posting_date', 'je.reference_id', 'jl.description', 'jl.debit', 'jl.credit']);

        $accounts = [];
        foreach ($rows as $r) {
            $accounts[$r->code] ??= ['code' => $r->code, 'label' => $r->label, 'lines' => [], 'debit' => 0.0, 'credit' => 0.0];
            $accounts[$r->code]['lines'][]  = $r;
            $accounts[$r->code]['debit']   += (float) $r->debit;
            $accounts[$r->code]['credit']  += (float) $r->credit;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.grand_livre', [
            'company'  => $company,
            'accounts' => array_values($accounts),
            'from'     => $request->input('from'),
            'to'       => $request->input('to'),
        ])->setPaper('a4');

        return $pdf->stream('grand-livre.pdf');
    }
}
