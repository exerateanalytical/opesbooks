<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\JournalPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Fiscal Year Close — OHADA SYSCOHADA requirement.
 *
 * Closing entry (clôture de l'exercice):
 *   Dr 131000  (Résultat net de l'exercice — clears the P&L result account)
 *   Cr 121000  (Report à nouveau créditeur — carries profit forward to retained earnings)
 *
 * For a loss (131000 has a credit balance):
 *   Dr 129000  (Report à nouveau débiteur — retained loss carry-forward)
 *   Cr 131000
 *
 * Opening Balances — allow importing prior-year balances when onboarding.
 */
class FiscalYearController extends Controller
{
    public function __construct(private JournalPostingService $poster) {}

    /**
     * Close fiscal year: carry net profit/loss to retained earnings.
     */
    public function close(Request $request, Company $company)
    {
        $data = $request->validate([
            'fiscal_year' => 'required|integer|min:2000|max:2099',
        ]);

        $year = $data['fiscal_year'];
        $from = "{$year}-01-01";
        $to   = "{$year}-12-31";

        // Sum all Class 6 (charges) and Class 7 (produits) movements
        $rows = DB::select("
            SELECT sa.class_digit,
                   SUM(jl.debit)  AS total_debit,
                   SUM(jl.credit) AS total_credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND je.posting_date BETWEEN ? AND ?
              AND je.deleted_at IS NULL
              AND sa.class_digit IN (6, 7)
            GROUP BY sa.class_digit
        ", [$company->id, $from, $to]);

        $byClass = [];
        foreach ($rows as $r) {
            $byClass[$r->class_digit] = $r;
        }

        $totalCharges = (float)($byClass[6]->total_debit  ?? 0) - (float)($byClass[6]->total_credit ?? 0);
        $totalProduits= (float)($byClass[7]->total_credit ?? 0) - (float)($byClass[7]->total_debit  ?? 0);
        $netResult    = $totalProduits - $totalCharges;

        if (abs($netResult) < 0.01) {
            return response()->json(['message' => 'Net result is zero — nothing to close.'], 422);
        }

        $closingLines = [];

        if ($netResult > 0) {
            // Profit: Dr 131000, Cr 121000
            $closingLines = [
                ['account_code' => '131000', 'debit' => $netResult, 'credit' => 0],
                ['account_code' => '121000', 'debit' => 0, 'credit' => $netResult],
            ];
        } else {
            // Loss: Dr 129000, Cr 131000
            $absLoss = abs($netResult);
            $closingLines = [
                ['account_code' => '129000', 'debit' => $absLoss, 'credit' => 0],
                ['account_code' => '131000', 'debit' => 0, 'credit' => $absLoss],
            ];
        }

        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => $to,
            'reference_id'    => 'CLOTURE-' . $year,
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Clôture exercice {$year} — " . ($netResult >= 0 ? 'Bénéfice' : 'Perte') . ' : ' . number_format(abs($netResult), 0, ',', ' ') . ' XAF',
            'posting_type'    => 'ADJUSTMENT',
        ], $closingLines);

        return response()->json([
            'message'      => "Exercice {$year} clôturé avec succès.",
            'net_result'   => round($netResult, 0),
            'type'         => $netResult >= 0 ? 'PROFIT' : 'LOSS',
            'journal_entry'=> $entry->id,
        ]);
    }

    /**
     * Import opening balances (soldes d'ouverture) when onboarding a company.
     * Posts a single balanced journal entry dated the first day of the fiscal year.
     */
    public function importOpeningBalances(Request $request, Company $company)
    {
        $data = $request->validate([
            'fiscal_year' => 'required|integer|min:2000|max:2099',
            'balances'    => 'required|array|min:1',
            'balances.*.account_code' => 'required|string',
            'balances.*.debit'        => 'required|numeric|min:0',
            'balances.*.credit'       => 'required|numeric|min:0',
        ]);

        // Verify the entry is balanced
        $totalDebit  = array_sum(array_column($data['balances'], 'debit'));
        $totalCredit = array_sum(array_column($data['balances'], 'credit'));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return response()->json([
                'message'       => 'Opening balances are not balanced.',
                'total_debit'   => round($totalDebit, 2),
                'total_credit'  => round($totalCredit, 2),
                'difference'    => round($totalDebit - $totalCredit, 2),
            ], 422);
        }

        $lines = array_map(fn($b) => [
            'account_code' => $b['account_code'],
            'debit'        => $b['debit'],
            'credit'       => $b['credit'],
        ], $data['balances']);

        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => $data['fiscal_year'] . '-01-01',
            'reference_id'    => 'OB-' . $data['fiscal_year'],
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Soldes d'ouverture {$data['fiscal_year']}",
            'posting_type'    => 'OPENING',
        ], $lines);

        return response()->json([
            'message'       => "Soldes d'ouverture {$data['fiscal_year']} importés.",
            'journal_entry' => $entry->id,
            'line_count'    => count($lines),
        ], 201);
    }
}
