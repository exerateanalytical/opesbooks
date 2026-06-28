<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\FinancialStatementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function __construct(private FinancialStatementService $svc) {}

    public function profitAndLoss(Request $request, Company $company): JsonResponse
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        return response()->json($this->svc->profitAndLoss($company, $request->from, $request->to));
    }

    public function balanceSheet(Request $request, Company $company): JsonResponse
    {
        $request->validate(['as_of' => 'required|date']);
        return response()->json($this->svc->balanceSheet($company, $request->as_of));
    }

    public function cashFlow(Request $request, Company $company): JsonResponse
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        return response()->json($this->svc->cashFlow($company, $request->from, $request->to));
    }

    public function agedReceivables(Company $company): JsonResponse
    {
        return response()->json($this->svc->agedReceivables($company));
    }

    public function agedPayables(Company $company): JsonResponse
    {
        return response()->json($this->svc->agedPayables($company));
    }

    // ── Printable PDF statements ───────────────────────────────────────────────

    /** GET /reports/pl/pdf?from&to */
    public function profitAndLossPdf(Request $request, Company $company)
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        $d = $this->svc->profitAndLoss($company, $request->from, $request->to);

        return $this->render($company, 'Compte de Résultat', "Du {$request->from} au {$request->to}", [
            ['heading' => 'Produits (Classe 7)', 'rows' => array_map(fn ($r) => ['code' => $r['code'], 'label' => $r['label'], 'amount' => $r['credit'] - $r['debit']], $d['revenue']), 'total' => $d['totals']['total_revenue_ht']],
            ['heading' => 'Charges (Classe 6)',  'rows' => array_map(fn ($r) => ['code' => $r['code'], 'label' => $r['label'], 'amount' => $r['debit'] - $r['credit']], $d['expenses']), 'total' => $d['totals']['total_expenses_ht']],
        ], ['label' => $d['totals']['net_result_label'], 'amount' => $d['totals']['net_result'], 'positive' => $d['totals']['net_result'] >= 0], "compte_resultat");
    }

    /** GET /reports/balance-sheet/pdf?as_of */
    public function balanceSheetPdf(Request $request, Company $company)
    {
        $request->validate(['as_of' => 'required|date']);
        $d = $this->svc->balanceSheet($company, $request->as_of);

        return $this->render($company, 'Bilan', "Au {$request->as_of}", [
            ['heading' => 'Actif',            'rows' => $d['assets']['items'],      'total' => $d['assets']['total']],
            ['heading' => 'Passif — Dettes',  'rows' => $d['liabilities']['items'], 'total' => $d['liabilities']['total']],
            ['heading' => 'Capitaux propres', 'rows' => $d['equity']['items'],      'total' => $d['equity']['total']],
        ], ['label' => $d['balanced'] ? 'Bilan équilibré ✓' : 'Déséquilibre', 'amount' => $d['assets']['total'], 'positive' => $d['balanced']], "bilan");
    }

    /** GET /reports/cash-flow/pdf?from&to */
    public function cashFlowPdf(Request $request, Company $company)
    {
        $request->validate(['from' => 'required|date', 'to' => 'required|date|after_or_equal:from']);
        $d = $this->svc->cashFlow($company, $request->from, $request->to);

        return $this->render($company, 'Tableau de Flux de Trésorerie', "Du {$request->from} au {$request->to}", [
            ['heading' => "Résultat d'exploitation", 'rows' => [['code' => '', 'label' => "Résultat d'exploitation (Produits − Charges)", 'amount' => $d['operating_result']]], 'total' => $d['operating_result']],
            ['heading' => 'Comptes de trésorerie (Classe 5)', 'rows' => array_map(fn ($r) => ['code' => $r['code'], 'label' => $r['label'], 'amount' => $r['balance']], $d['treasury_accounts']), 'total' => $d['net_cash_flow']],
        ], ['label' => 'Trésorerie nette', 'amount' => $d['net_cash_flow'], 'positive' => $d['net_cash_flow'] >= 0], "flux_tresorerie");
    }

    /** GET /reports/aged-receivables/pdf */
    public function agedReceivablesPdf(Company $company)
    {
        return $this->renderAged($company, 'Balance Âgée des Créances (Clients)', $this->svc->agedReceivables($company), 'creances_agees');
    }

    /** GET /reports/aged-payables/pdf */
    public function agedPayablesPdf(Company $company)
    {
        return $this->renderAged($company, 'Balance Âgée des Dettes (Fournisseurs)', $this->svc->agedPayables($company), 'dettes_agees');
    }

    private function renderAged(Company $company, string $title, array $data, string $file)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.aged', compact('company', 'title', 'data'))->setPaper('a4');
        return $pdf->stream("{$file}.pdf");
    }

    private function render(Company $company, string $title, string $subtitle, array $groups, array $highlight, string $file)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.statement', compact('company', 'title', 'subtitle', 'groups', 'highlight'))->setPaper('a4');
        return $pdf->stream("{$file}.pdf");
    }
}
