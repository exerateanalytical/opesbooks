<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CSV/data exports for reporting.
 */
class ExportController extends Controller
{
    /**
     * Trial balance as CSV stream.
     */
    public function trialBalanceCsv(Request $request, Company $company)
    {
        $from = $request->input('from', date('Y') . '-01-01');
        $to   = $request->input('to',   date('Y') . '-12-31');

        $rows = DB::select("
            SELECT sa.code, sa.label, sa.class_digit,
                   COALESCE(SUM(jl.debit), 0)  AS total_debit,
                   COALESCE(SUM(jl.credit), 0) AS total_credit
            FROM syscohada_accounts sa
            LEFT JOIN journal_lines jl ON jl.syscohada_account_id = sa.id
            LEFT JOIN journal_entries je ON jl.journal_entry_id = je.id
                AND je.company_id = ?
                AND je.posting_date BETWEEN ? AND ?
                AND je.deleted_at IS NULL
            GROUP BY sa.code, sa.label, sa.class_digit
            ORDER BY sa.code
        ", [$company->id, $from, $to]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"balance_generale_{$from}_{$to}.csv\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Compte', 'Intitulé', 'Classe', 'Débit Cumulé', 'Crédit Cumulé', 'Solde Débiteur', 'Solde Créditeur'], ';');
            foreach ($rows as $r) {
                $soldeD = max(0, $r->total_debit - $r->total_credit);
                $soldeC = max(0, $r->total_credit - $r->total_debit);
                fputcsv($out, [
                    $r->code, $r->label, $r->class_digit,
                    number_format($r->total_debit, 2, ',', ''),
                    number_format($r->total_credit, 2, ',', ''),
                    number_format($soldeD, 2, ',', ''),
                    number_format($soldeC, 2, ',', ''),
                ], ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Journal entries as CSV.
     */
    public function journalCsv(Request $request, Company $company)
    {
        $from = $request->input('from', date('Y') . '-01-01');
        $to   = $request->input('to',   date('Y') . '-12-31');

        $rows = DB::select("
            SELECT je.posting_date, je.reference_id, je.memo, je.posting_type,
                   sa.code, sa.label, jl.debit, jl.credit
            FROM journal_lines jl
            JOIN journal_entries je ON jl.journal_entry_id = je.id
            JOIN syscohada_accounts sa ON jl.syscohada_account_id = sa.id
            WHERE je.company_id = ?
              AND je.posting_date BETWEEN ? AND ?
              AND je.deleted_at IS NULL
            ORDER BY je.posting_date, je.id, jl.id
        ", [$company->id, $from, $to]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"journal_{$from}_{$to}.csv\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Date', 'Référence', 'Libellé', 'Type', 'Compte', 'Intitulé Compte', 'Débit', 'Crédit'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->posting_date, $r->reference_id, $r->memo, $r->posting_type,
                    $r->code, $r->label,
                    number_format($r->debit, 2, ',', ''),
                    number_format($r->credit, 2, ',', ''),
                ], ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Aged receivables as CSV from customer_invoices.
     */
    public function agedReceivablesCsv(Request $request, Company $company)
    {
        $today = now()->toDateString();

        $rows = DB::select("
            SELECT ci.invoice_number, c.name AS customer_name,
                   ci.invoice_date, ci.due_date, ci.amount_ttc, ci.status,
                   CAST(julianday(?) - julianday(ci.due_date) AS INTEGER) AS days_overdue
            FROM customer_invoices ci
            JOIN customers c ON ci.customer_id = c.id
            WHERE ci.company_id = ?
              AND ci.status IN ('SENT','OVERDUE')
              AND ci.deleted_at IS NULL
            ORDER BY ci.due_date
        ", [$today, $company->id]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aged_receivables.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Client', 'Facture', 'Date Facture', 'Échéance', 'Montant TTC', 'Statut', 'Jours de Retard'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->customer_name, $r->invoice_number, $r->invoice_date, $r->due_date,
                    number_format($r->amount_ttc, 2, ',', ''),
                    $r->status,
                    max(0, $r->days_overdue),
                ], ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Aged payables as CSV from supplier_invoices.
     */
    public function agedPayablesCsv(Request $request, Company $company)
    {
        $today = now()->toDateString();

        $rows = DB::select("
            SELECT si.invoice_number, s.name AS supplier_name,
                   si.invoice_date, si.due_date, si.net_payable, si.status,
                   CAST(julianday(?) - julianday(si.due_date) AS INTEGER) AS days_overdue
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            WHERE si.company_id = ?
              AND si.status IN ('RECEIVED','APPROVED','OVERDUE')
              AND si.deleted_at IS NULL
            ORDER BY si.due_date
        ", [$today, $company->id]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aged_payables.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Fournisseur', 'Facture', 'Date Facture', 'Échéance', 'Net à Payer', 'Statut', 'Jours de Retard'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->supplier_name, $r->invoice_number, $r->invoice_date, $r->due_date,
                    number_format($r->net_payable, 2, ',', ''),
                    $r->status,
                    max(0, $r->days_overdue),
                ], ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
