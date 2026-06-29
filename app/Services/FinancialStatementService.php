<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class FinancialStatementService
{
    /**
     * Profit & Loss (Compte de Résultat) — SYSCOHADA classes 6 & 7.
     */
    public function profitAndLoss(Company $company, string $from, string $to): array
    {
        $rows = $this->aggregateLines($company->id, $from, $to);

        $revenue  = $this->sumClass($rows, '7');
        $expenses = $this->sumClass($rows, '6');
        $result   = $revenue - $expenses;

        return [
            'period'   => ['from' => $from, 'to' => $to],
            'revenue'  => $this->classBreakdown($rows, '7'),
            'expenses' => $this->classBreakdown($rows, '6'),
            'totals'   => [
                'total_revenue_ht'  => round($revenue, 2),
                'total_expenses_ht' => round($expenses, 2),
                'net_result'        => round($result, 2),
                'net_result_label'  => $result >= 0 ? 'Bénéfice' : 'Perte',
            ],
        ];
    }

    /**
     * Balance Sheet (Bilan) — assets (classes 1-5 debit) vs liabilities (classes 1-4 credit).
     */
    public function balanceSheet(Company $company, string $asOf): array
    {
        $rows = $this->aggregateLines($company->id, '1900-01-01', $asOf);

        $assets      = $this->bsAssets($rows);
        $liabilities = $this->bsLiabilities($rows);
        $equity      = $this->bsEquity($rows);

        return [
            'as_of'       => $asOf,
            'assets'      => $assets,
            'liabilities' => $liabilities,
            'equity'      => $equity,
            'balanced'    => abs($assets['total'] - ($liabilities['total'] + $equity['total'])) < 1,
        ];
    }

    /**
     * Cash Flow summary (simplified — from Class 5 treasury movements).
     */
    public function cashFlow(Company $company, string $from, string $to): array
    {
        $rows = $this->aggregateLines($company->id, $from, $to);

        $operating = $this->sumClass($rows, '7') - $this->sumClass($rows, '6');
        $treasury  = $this->classBreakdown($rows, '5');

        return [
            'period'            => ['from' => $from, 'to' => $to],
            'operating_result'  => round($operating, 2),
            'treasury_accounts' => $treasury,
            'net_cash_flow'     => round(collect($treasury)->sum('balance'), 2),
        ];
    }

    /**
     * Aged receivables — outstanding customer invoices grouped by aging bucket.
     */
    public function agedReceivables(Company $company): array
    {
        return $this->agedAnalysis($company->id, 'customer_invoices', 'customer_id', 'customers');
    }

    /**
     * Aged payables: queries journal lines on account 401xxx (Fournisseurs)
     * grouped by posting date bucket to approximate overdue payables.
     */
    public function agedPayables(Company $company): array
    {
        $rows = DB::select("
            SELECT
                sa.code,
                sa.label,
                je.posting_date,
                je.memo,
                SUM(jl.credit - jl.debit) AS balance,
                CAST(julianday('now') - julianday(je.posting_date) AS INTEGER) AS days_old
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND sa.code LIKE '401%'
              AND je.deleted_at IS NULL
            GROUP BY je.id, sa.code, sa.label
            HAVING balance > 0
            ORDER BY je.posting_date ASC
        ", [$company->id]);

        $buckets = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        $invoices = [];
        foreach ($rows as $r) {
            $days = (int)$r->days_old;
            $bucket = match(true) {
                $days <= 0  => 'current',
                $days <= 30 => '1_30',
                $days <= 60 => '31_60',
                $days <= 90 => '61_90',
                default     => 'over_90',
            };
            $buckets[$bucket] += (float)$r->balance;
            $invoices[] = [
                'customer' => ['name' => $r->code.' '.$r->label],
                'invoice_number' => $r->code,
                'amount_ttc' => round((float)$r->balance, 2),
                'due_date' => $r->posting_date,
                'days_overdue' => $days,
                'memo' => $r->memo,
            ];
        }

        return array_merge(
            array_map(fn($v) => round($v, 2), $buckets),
            ['invoices' => $invoices, 'grand_total' => round(array_sum($buckets), 2)]
        );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function aggregateLines(int $companyId, string $from, string $to): array
    {
        return DB::select("
            SELECT
                sa.code,
                sa.label,
                SUM(jl.debit)  AS total_debit,
                SUM(jl.credit) AS total_credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND je.posting_date BETWEEN ? AND ?
              AND je.deleted_at IS NULL
              AND je.transaction_status = 'SUCCESSFUL'
            GROUP BY sa.code, sa.label
            ORDER BY sa.code
        ", [$companyId, $from, $to]);
    }

    private function sumClass(array $rows, string $class): float
    {
        $sum = 0;
        foreach ($rows as $r) {
            if (!str_starts_with($r->code, $class)) continue;
            // Revenue class 7: credit side; expense class 6: debit side
            $sum += $class === '7'
                ? (float)$r->total_credit - (float)$r->total_debit
                : (float)$r->total_debit  - (float)$r->total_credit;
        }
        return $sum;
    }

    private function classBreakdown(array $rows, string $class): array
    {
        $out = [];
        foreach ($rows as $r) {
            if (!str_starts_with($r->code, $class)) continue;
            $debit  = (float)$r->total_debit;
            $credit = (float)$r->total_credit;
            $out[] = [
                'code'    => $r->code,
                'label'   => $r->label,
                'debit'   => round($debit, 2),
                'credit'  => round($credit, 2),
                'balance' => round($debit - $credit, 2),
            ];
        }
        return $out;
    }

    private function bsAssets(array $rows): array
    {
        $classes = ['1', '2', '3', '4', '5'];
        $items = []; $total = 0;
        foreach ($rows as $r) {
            $class = substr($r->code, 0, 1);
            if (!in_array($class, $classes)) continue;
            $balance = (float)$r->total_debit - (float)$r->total_credit;
            if ($balance <= 0) continue;
            $items[] = ['code' => $r->code, 'label' => $r->label, 'amount' => round($balance, 2)];
            $total += $balance;
        }
        return ['items' => $items, 'total' => round($total, 2)];
    }

    private function bsLiabilities(array $rows): array
    {
        $classes = ['1', '2', '3', '4'];
        $items = []; $total = 0;
        foreach ($rows as $r) {
            $class = substr($r->code, 0, 1);
            if (!in_array($class, $classes)) continue;
            $balance = (float)$r->total_credit - (float)$r->total_debit;
            if ($balance <= 0) continue;
            $items[] = ['code' => $r->code, 'label' => $r->label, 'amount' => round($balance, 2)];
            $total += $balance;
        }
        return ['items' => $items, 'total' => round($total, 2)];
    }

    private function bsEquity(array $rows): array
    {
        $items = []; $total = 0;
        foreach ($rows as $r) {
            if (!str_starts_with($r->code, '1')) continue;
            $balance = (float)$r->total_credit - (float)$r->total_debit;
            if ($balance <= 0) continue;
            $items[] = ['code' => $r->code, 'label' => $r->label, 'amount' => round($balance, 2)];
            $total += $balance;
        }
        return ['items' => $items, 'total' => round($total, 2)];
    }

    private function agedAnalysis(int $companyId, string $table, string $fk, string $joinTable): array
    {
        $rows = DB::select("
            SELECT
                c.name                  AS customer_name,
                ci.id,
                ci.invoice_number,
                ci.invoice_date,
                ci.due_date,
                ci.amount_ttc,
                ci.status,
                CAST(julianday('now') - julianday(ci.due_date) AS INTEGER) AS days_overdue
            FROM {$table} ci
            JOIN {$joinTable} c ON ci.{$fk} = c.id
            WHERE ci.company_id = ?
              AND ci.status IN ('SENT','OVERDUE')
              AND ci.deleted_at IS NULL
            ORDER BY ci.due_date ASC
        ", [$companyId]);

        $buckets = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        $invoices = [];
        foreach ($rows as $r) {
            $days = (int)$r->days_overdue;
            $bucket = match(true) {
                $days <= 0  => 'current',
                $days <= 30 => '1_30',
                $days <= 60 => '31_60',
                $days <= 90 => '61_90',
                default     => 'over_90',
            };
            $buckets[$bucket] += (float)$r->amount_ttc;
            $inv = (array)$r;
            $inv['customer'] = ['name' => $r->customer_name];
            $invoices[] = $inv;
        }

        return array_merge(
            array_map(fn($v) => round($v, 2), $buckets),
            ['invoices' => $invoices, 'grand_total' => round(array_sum($buckets), 2)]
        );
    }
}
