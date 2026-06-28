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
}
