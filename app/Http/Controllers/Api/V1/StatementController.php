<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Printable statements for customers (relevé de compte client)
 * and suppliers (relevé de compte fournisseur).
 */
class StatementController extends Controller
{
    public function customerStatement(Request $request, Company $company, Customer $customer)
    {
        abort_if($customer->company_id !== $company->id, 404);

        $from = $request->input('from', date('Y') . '-01-01');
        $to   = $request->input('to',   date('Y') . '-12-31');

        $invoices = DB::select("
            SELECT ci.invoice_number, ci.invoice_date, ci.due_date,
                   ci.amount_ht, ci.tva_amount, ci.cac_amount, ci.amount_ttc,
                   ci.status, ci.paid_at
            FROM customer_invoices ci
            WHERE ci.company_id = ?
              AND ci.customer_id = ?
              AND ci.invoice_date BETWEEN ? AND ?
              AND ci.deleted_at IS NULL
            ORDER BY ci.invoice_date
        ", [$company->id, $customer->id, $from, $to]);

        $totalInvoiced  = array_sum(array_column((array)$invoices, 'amount_ttc'));
        $totalPaid      = array_sum(array_map(fn($i) => $i->status === 'PAID' ? $i->amount_ttc : 0, $invoices));
        $totalOutstanding = $totalInvoiced - $totalPaid;

        $pdf = Pdf::loadView('statements.customer', [
            'company'         => $company,
            'customer'        => $customer,
            'invoices'        => $invoices,
            'from'            => $from,
            'to'              => $to,
            'total_invoiced'  => $totalInvoiced,
            'total_paid'      => $totalPaid,
            'total_outstanding'=> $totalOutstanding,
            'generated_at'    => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        return $pdf->download("releve_client_{$customer->id}_{$from}_{$to}.pdf");
    }

    public function supplierStatement(Request $request, Company $company, Supplier $supplier)
    {
        abort_if($supplier->company_id !== $company->id, 404);

        $from = $request->input('from', date('Y') . '-01-01');
        $to   = $request->input('to',   date('Y') . '-12-31');

        $invoices = DB::select("
            SELECT si.invoice_number, si.invoice_date, si.due_date,
                   si.amount_ht, si.tva_amount, si.amount_ttc, si.net_payable,
                   si.withholding_amount, si.status, si.paid_at
            FROM supplier_invoices si
            WHERE si.company_id = ?
              AND si.supplier_id = ?
              AND si.invoice_date BETWEEN ? AND ?
              AND si.deleted_at IS NULL
            ORDER BY si.invoice_date
        ", [$company->id, $supplier->id, $from, $to]);

        $totalDue  = array_sum(array_column((array)$invoices, 'net_payable'));
        $totalPaid = array_sum(array_map(fn($i) => $i->status === 'PAID' ? $i->net_payable : 0, $invoices));
        $balance   = $totalDue - $totalPaid;

        $pdf = Pdf::loadView('statements.supplier', [
            'company'    => $company,
            'supplier'   => $supplier,
            'invoices'   => $invoices,
            'from'       => $from,
            'to'         => $to,
            'total_due'  => $totalDue,
            'total_paid' => $totalPaid,
            'balance'    => $balance,
            'generated_at'=> now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        return $pdf->download("releve_fournisseur_{$supplier->id}_{$from}_{$to}.pdf");
    }
}
