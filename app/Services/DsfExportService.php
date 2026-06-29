<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

/**
 * DSF — Déclaration Statistique et Fiscale
 * Annual filing required by DGI Cameroun for all entities under Régime Réel.
 * Generates the data payload for Liasse Fiscale tables 1–4.
 */
class DsfExportService
{
    public function __construct(private FinancialStatementService $statements) {}

    public function generate(Company $company, int $fiscalYear): array
    {
        $from = "{$fiscalYear}-01-01";
        $to   = "{$fiscalYear}-12-31";

        $pl = $this->statements->profitAndLoss($company, $from, $to);
        $bs = $this->statements->balanceSheet($company, "{$fiscalYear}-12-31");

        $rows = DB::select("
            SELECT sa.code, sa.label, sa.class_digit,
                   SUM(jl.debit)  AS total_debit,
                   SUM(jl.credit) AS total_credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND je.posting_date BETWEEN ? AND ?
              AND je.deleted_at IS NULL
            GROUP BY sa.code, sa.label, sa.class_digit
            ORDER BY sa.code
        ", [$company->id, $from, $to]);

        $byCode = [];
        foreach ($rows as $r) {
            $byCode[$r->code] = $r;
        }

        $get = fn(string $code, string $side) =>
            round((float)($byCode[$code]?->{'total_'.$side} ?? 0), 0);

        return [
            'meta' => [
                'type'           => 'DSF_LIASSE_FISCALE',
                'fiscal_year'    => $fiscalYear,
                'generated_at'   => now()->toIso8601String(),
                'company_name'   => $company->name,
                'company_niu'    => $company->niu,
                'company_rccm'   => $company->rccm,
                'tax_center'     => $company->tax_center,
                'tax_regime'     => $company->tax_regime,
                'address'        => $company->address,
            ],

            // TABLE 1 — COMPTE DE RÉSULTAT (P&L)
            'table_1_compte_resultat' => [
                'chiffre_affaires_ht'     => round((float)($pl['revenue'] ?? 0), 0),
                'achats_consommes'        => $get('601100','debit') + $get('602100','debit'),
                'charges_personnel'       => $get('661100','debit') + $get('664000','debit'),
                'dotations_amortissements'=> $get('681200','debit'),
                'autres_charges'          => round((float)($pl['expenses'] ?? 0) - $get('661100','debit') - $get('664000','debit') - $get('681200','debit'), 0),
                'resultat_exploitation'   => round((float)($pl['revenue'] ?? 0) - (float)($pl['expenses'] ?? 0), 0),
                'resultat_net'            => round((float)($pl['net_profit'] ?? 0), 0),
                'tva_collectee'           => $get('443100','credit'),
                'tva_deductible'          => $get('445200','debit') + $get('445100','debit'),
                'tva_nette_due'           => $get('443100','credit') - $get('445200','debit') - $get('445100','debit'),
                // 447000 = salary withholdings owed to the State (IRPP + CAC + RAV);
                // 447100 = supplier précompte (advance IR on purchases) — a distinct tax.
                'irpp_retenu'             => $get('447000','credit'),
                'precompte_retenu'        => $get('447100','credit'),
                'cnps_salarie'            => $get('431000','credit'),
            ],

            // TABLE 2 — BILAN ACTIF
            'table_2_bilan_actif' => $bs['assets'] ?? [],

            // TABLE 3 — BILAN PASSIF
            'table_3_bilan_passif' => $bs['liabilities'] ?? [],

            // TABLE 4 — BALANCE GÉNÉRALE (grand livre summary)
            'table_4_balance_generale' => array_map(fn($r) => [
                'compte'          => $r->code,
                'intitule'        => $r->label,
                'classe'          => $r->class_digit,
                'debit_cumul'     => round((float)$r->total_debit, 0),
                'credit_cumul'    => round((float)$r->total_credit, 0),
                'solde_debiteur'  => round(max(0, (float)$r->total_debit - (float)$r->total_credit), 0),
                'solde_crediteur' => round(max(0, (float)$r->total_credit - (float)$r->total_debit), 0),
            ], $rows),

            // TABLE 5 — EFFECTIFS & MASSE SALARIALE
            'table_5_effectifs' => [
                'nombre_salaries'  => DB::table('employees')->where('company_id', $company->id)->where('is_active', true)->count(),
                'masse_salariale'  => $get('661100','debit'),
                'charges_cnps'     => $get('664000','debit'),
                'total_irpp_verse' => $get('447000','credit'),
            ],
        ];
    }

    /**
     * Generate TVA monthly return (Déclaration D10) data for a given month.
     */
    public function monthlyTvaReturn(Company $company, int $month, int $year): array
    {
        $from = sprintf('%d-%02d-01', $year, $month);
        $to   = sprintf('%d-%02d-%d', $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

        $rows = DB::select("
            SELECT sa.code,
                   SUM(jl.debit)  AS total_debit,
                   SUM(jl.credit) AS total_credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND je.posting_date BETWEEN ? AND ?
              AND je.deleted_at IS NULL
              AND sa.code IN ('443100','445100','445200','445400','448600')
            GROUP BY sa.code
        ", [$company->id, $from, $to]);

        $byCode = [];
        foreach ($rows as $r) $byCode[$r->code] = $r;
        $get = fn($code, $side) => round((float)($byCode[$code]?->{'total_'.$side} ?? 0), 0);

        $tvaCollectee  = $get('443100','credit');
        $tvaDeductible = $get('445200','debit') + $get('445100','debit') + $get('445400','debit');
        $cacCollecte   = $get('448600','credit');
        $tvaNette      = max(0, $tvaCollectee - $tvaDeductible);
        $cacNet        = round($tvaNette * 0.10, 0);

        return [
            'period'         => sprintf('%02d/%d', $month, $year),
            'company_niu'    => $company->niu,
            'company_name'   => $company->name,
            'tax_center'     => $company->tax_center,
            'tva_collectee'  => $tvaCollectee,
            'tva_deductible' => $tvaDeductible,
            'tva_nette_due'  => $tvaNette,
            'cac_collecte'   => $cacCollecte,
            'cac_net_du'     => $cacNet,
            'total_a_payer'  => $tvaNette + $cacNet,
        ];
    }
}
