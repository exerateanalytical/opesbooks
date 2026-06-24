<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Returns budget vs actuals for every account in the budget,
     * with variance (amount and %) per month and for the full year.
     */
    public function variance(Budget $budget): array
    {
        $company = $budget->company;
        $year    = $budget->fiscal_year;

        // Actual movements from journal for the fiscal year
        $actuals = DB::select("
            SELECT sa.code, sa.class_digit,
                   CAST(strftime('%m', je.posting_date) AS INTEGER) AS month,
                   SUM(jl.debit)  AS total_debit,
                   SUM(jl.credit) AS total_credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND strftime('%Y', je.posting_date) = ?
              AND je.deleted_at IS NULL
            GROUP BY sa.code, sa.class_digit, month
        ", [$company->id, (string)$year]);

        // Index actuals by code+month
        $actualMap = [];
        foreach ($actuals as $a) {
            $net = in_array($a->class_digit, [6])
                ? (float)$a->total_debit - (float)$a->total_credit   // expenses: debit side
                : (float)$a->total_credit - (float)$a->total_debit;  // revenue: credit side
            $actualMap[$a->code][$a->month] = ($actualMap[$a->code][$a->month] ?? 0) + $net;
        }

        $lines = $budget->lines()->get()->groupBy('account_code');
        $result = [];

        foreach ($lines as $code => $budgetLines) {
            $monthly = [];
            $totalBudget = 0;
            $totalActual = 0;

            for ($m = 1; $m <= 12; $m++) {
                $budgeted = (float)($budgetLines->firstWhere('period_month', $m)?->budgeted_amount ?? 0);
                $actual   = (float)($actualMap[$code][$m] ?? 0);
                $variance = $actual - $budgeted;

                $monthly[$m] = [
                    'budgeted' => round($budgeted, 0),
                    'actual'   => round($actual, 0),
                    'variance' => round($variance, 0),
                    'pct'      => $budgeted != 0 ? round(($variance / $budgeted) * 100, 1) : null,
                ];

                $totalBudget += $budgeted;
                $totalActual += $actual;
            }

            $result[] = [
                'account_code'   => $code,
                'monthly'        => $monthly,
                'total_budgeted' => round($totalBudget, 0),
                'total_actual'   => round($totalActual, 0),
                'total_variance' => round($totalActual - $totalBudget, 0),
                'total_pct'      => $totalBudget != 0 ? round((($totalActual - $totalBudget) / $totalBudget) * 100, 1) : null,
            ];
        }

        return ['fiscal_year' => $year, 'lines' => $result];
    }
}
