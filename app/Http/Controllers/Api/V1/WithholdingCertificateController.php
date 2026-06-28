<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Attestation de Précompte — printable withholding tax certificate per supplier/period.
 * Required legal document for DGE/CIME companies (5.5% withholding on purchases).
 */
class WithholdingCertificateController extends Controller
{
    public function generate(Request $request, Company $company, Supplier $supplier)
    {
        abort_if($supplier->company_id !== $company->id, 404);

        $from = $request->input('from', date('Y') . '-01-01');
        $to   = $request->input('to',   date('Y') . '-12-31');

        $invoices = DB::select("
            SELECT invoice_number, invoice_date, amount_ht, tva_amount,
                   withholding_amount, net_payable, status
            FROM supplier_invoices
            WHERE company_id = ? AND supplier_id = ?
              AND invoice_date BETWEEN ? AND ?
              AND withholding_amount > 0
              AND deleted_at IS NULL
            ORDER BY invoice_date
        ", [$company->id, $supplier->id, $from, $to]);

        $totalHt          = array_sum(array_column((array)$invoices, 'amount_ht'));
        $totalWithholding = array_sum(array_column((array)$invoices, 'withholding_amount'));

        $pdf = Pdf::loadView('certificates.withholding', [
            'company'          => $company,
            'supplier'         => $supplier,
            'invoices'         => $invoices,
            'from'             => $from,
            'to'               => $to,
            'total_ht'         => $totalHt,
            'total_withholding'=> $totalWithholding,
            'generated_at'     => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        return $pdf->stream("attestation_precompte_{$supplier->id}_{$from}_{$to}.pdf");
    }
}
