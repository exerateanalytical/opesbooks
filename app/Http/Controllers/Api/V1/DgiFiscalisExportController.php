<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SyscohadaAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DgiFiscalisExportController extends Controller
{
    /**
     * GET /api/v1/exports/dgi-fiscalis
     *
     * Compiles a complete DGI-ready matrix aggregating all POSTED journal_lines
     * per SYSCOHADA account for the requested fiscal period.
     * Output is structured for direct import into the Cameroon DGI portal.
     */
    public function export(Request $request, Company $company): JsonResponse
    {
        $request->validate([
            'fiscal_year' => 'required|integer|min:2000|max:2099',
        ]);

        $year     = (int) $request->input('fiscal_year');
        $dateFrom = "{$year}-01-01";
        $dateTo   = "{$year}-12-31";

        $accounts = SyscohadaAccount::select(
                'syscohada_accounts.code',
                'syscohada_accounts.label',
                'syscohada_accounts.class_digit'
            )
            ->selectRaw('COALESCE(SUM(journal_lines.debit), 0)  AS total_debit')
            ->selectRaw('COALESCE(SUM(journal_lines.credit), 0) AS total_credit')
            ->leftJoin('journal_lines', 'journal_lines.syscohada_account_id', '=', 'syscohada_accounts.id')
            ->leftJoin('journal_entries', function ($join) use ($company, $dateFrom, $dateTo) {
                $join->on('journal_entries.id', '=', 'journal_lines.journal_entry_id')
                    ->where('journal_entries.company_id', $company->id)
                    ->whereIn('journal_entries.transaction_status', ['SUCCESSFUL'])
                    ->whereDate('journal_entries.posting_date', '>=', $dateFrom)
                    ->whereDate('journal_entries.posting_date', '<=', $dateTo);
            })
            ->groupBy('syscohada_accounts.code', 'syscohada_accounts.label', 'syscohada_accounts.class_digit')
            ->orderBy('syscohada_accounts.code')
            ->get();

        $grandDebit  = $accounts->sum('total_debit');
        $grandCredit = $accounts->sum('total_credit');
        $isBalanced  = abs($grandDebit - $grandCredit) < 0.01;

        // DGI matrix — only include accounts with movement
        $matrix = $accounts
            ->filter(fn ($a) => $a->total_debit > 0 || $a->total_credit > 0)
            ->map(fn ($a) => [
                'compte'           => $a->code,
                'intitule'         => $a->label,
                'classe'           => $a->class_digit,
                'debit_xaf'        => number_format((float) $a->total_debit, 2, '.', ''),
                'credit_xaf'       => number_format((float) $a->total_credit, 2, '.', ''),
                'solde_debiteur'   => $a->total_debit  > $a->total_credit  ? number_format($a->total_debit  - $a->total_credit,  2, '.', '') : '0.00',
                'solde_crediteur'  => $a->total_credit > $a->total_debit   ? number_format($a->total_credit - $a->total_debit,   2, '.', '') : '0.00',
            ])
            ->values();

        return response()->json([
            'export_type'    => 'DGI_FISCALIS_BALANCE_GENERALE',
            'company_niu'    => $company->niu,
            'company_name'   => $company->name,
            'tax_center'     => $company->tax_center,
            'fiscal_year'    => $year,
            'generated_at'   => now()->toIso8601String(),
            'grand_debit'    => number_format($grandDebit, 2, '.', ''),
            'grand_credit'   => number_format($grandCredit, 2, '.', ''),
            'balanced'       => $isBalanced,
            'accounts_count' => $matrix->count(),
            'data'           => $matrix,
        ]);
    }
}
