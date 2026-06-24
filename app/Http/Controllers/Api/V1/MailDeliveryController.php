<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MailDeliveryController extends Controller
{
    // POST /companies/{company}/mail/invoice/{invoice}
    public function sendInvoice(Request $request, Company $company, CustomerInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);

        $data = $request->validate([
            'to'      => 'nullable|email',   // override recipient; defaults to customer email
            'cc'      => 'nullable|email',
            'message' => 'nullable|string|max:500',
        ]);

        $customer = $invoice->customer;
        $to       = $data['to'] ?? $customer->email;
        abort_if(!$to, 422, 'No email address on file for this customer.');

        $pdf = Pdf::loadView('invoices.invoice', [
            'company'  => $company,
            'customer' => $customer,
            'invoice'  => $invoice,
        ])->setPaper('a4');

        $pdfContent  = $pdf->output();
        $filename    = "facture_{$invoice->invoice_number}.pdf";
        $customMsg   = $data['message'] ?? null;

        Mail::html(
            $this->invoiceEmailBody($company, $invoice, $customMsg),
            function ($mail) use ($to, $data, $company, $invoice, $pdfContent, $filename) {
                $mail->to($to)
                     ->subject("Facture {$invoice->invoice_number} — {$company->name}")
                     ->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
                if (!empty($data['cc'])) {
                    $mail->cc($data['cc']);
                }
            }
        );

        return response()->json(['message' => "Invoice {$invoice->invoice_number} sent to {$to}."]);
    }

    // POST /companies/{company}/mail/customer-statement/{customer}
    public function sendCustomerStatement(Request $request, Company $company, Customer $customer)
    {
        abort_if($customer->company_id !== $company->id, 404);

        $data = $request->validate([
            'to'   => 'nullable|email',
            'from' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $to   = $data['to'] ?? $customer->email;
        abort_if(!$to, 422, 'No email address on file for this customer.');

        $from    = $data['from']    ?? date('Y') . '-01-01';
        $toDate  = $data['to_date'] ?? date('Y') . '-12-31';

        $invoices = DB::select("
            SELECT ci.invoice_number, ci.invoice_date, ci.due_date,
                   ci.amount_ht, ci.tva_amount, ci.cac_amount, ci.amount_ttc, ci.status
            FROM customer_invoices ci
            WHERE ci.company_id = ? AND ci.customer_id = ?
              AND ci.invoice_date BETWEEN ? AND ? AND ci.deleted_at IS NULL
            ORDER BY ci.invoice_date
        ", [$company->id, $customer->id, $from, $toDate]);

        $totalInvoiced    = array_sum(array_column((array)$invoices, 'amount_ttc'));
        $totalPaid        = array_sum(array_map(fn($i) => $i->status === 'PAID' ? $i->amount_ttc : 0, $invoices));
        $totalOutstanding = $totalInvoiced - $totalPaid;

        $pdf = Pdf::loadView('statements.customer', [
            'company'          => $company,
            'customer'         => $customer,
            'invoices'         => $invoices,
            'from'             => $from,
            'to'               => $toDate,
            'total_invoiced'   => $totalInvoiced,
            'total_paid'       => $totalPaid,
            'total_outstanding'=> $totalOutstanding,
            'generated_at'     => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        $pdfContent = $pdf->output();
        $filename   = "releve_{$customer->id}_{$from}_{$toDate}.pdf";

        Mail::html(
            "<p>Veuillez trouver ci-joint votre relevé de compte pour la période du {$from} au {$toDate}.</p><p>Cordialement,<br>{$company->name}</p>",
            function ($mail) use ($to, $company, $customer, $pdfContent, $filename, $from, $toDate) {
                $mail->to($to)
                     ->subject("Relevé de compte — {$company->name} — {$from} au {$toDate}")
                     ->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
            }
        );

        return response()->json(['message' => "Statement sent to {$to}."]);
    }

    // POST /companies/{company}/mail/supplier-statement/{supplier}
    public function sendSupplierStatement(Request $request, Company $company, Supplier $supplier)
    {
        abort_if($supplier->company_id !== $company->id, 404);

        $data = $request->validate([
            'to'      => 'nullable|email',
            'from'    => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $to      = $data['to'] ?? $supplier->email;
        abort_if(!$to, 422, 'No email address on file for this supplier.');

        $from   = $data['from']    ?? date('Y') . '-01-01';
        $toDate = $data['to_date'] ?? date('Y') . '-12-31';

        $invoices  = DB::select("
            SELECT invoice_number, invoice_date, due_date,
                   amount_ht, tva_amount, withholding_amount, net_payable, status
            FROM supplier_invoices
            WHERE company_id = ? AND supplier_id = ?
              AND invoice_date BETWEEN ? AND ? AND deleted_at IS NULL
            ORDER BY invoice_date
        ", [$company->id, $supplier->id, $from, $toDate]);

        $totalDue  = array_sum(array_column((array)$invoices, 'net_payable'));
        $totalPaid = array_sum(array_map(fn($i) => $i->status === 'PAID' ? $i->net_payable : 0, $invoices));
        $balance   = $totalDue - $totalPaid;

        $pdf = Pdf::loadView('statements.supplier', [
            'company'    => $company,
            'supplier'   => $supplier,
            'invoices'   => $invoices,
            'from'       => $from,
            'to'         => $toDate,
            'total_due'  => $totalDue,
            'total_paid' => $totalPaid,
            'balance'    => $balance,
            'generated_at' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        $pdfContent = $pdf->output();
        $filename   = "releve_fournisseur_{$supplier->id}_{$from}_{$toDate}.pdf";

        Mail::html(
            "<p>Veuillez trouver ci-joint votre relevé de compte fournisseur pour la période du {$from} au {$toDate}.</p><p>Cordialement,<br>{$company->name}</p>",
            function ($mail) use ($to, $company, $supplier, $pdfContent, $filename, $from, $toDate) {
                $mail->to($to)
                     ->subject("Relevé Fournisseur — {$company->name} — {$from} au {$toDate}")
                     ->attachData($pdfContent, $filename, ['mime' => 'application/pdf']);
            }
        );

        return response()->json(['message' => "Supplier statement sent to {$to}."]);
    }

    private function invoiceEmailBody(Company $company, CustomerInvoice $invoice, ?string $customMsg): string
    {
        $amount = number_format($invoice->amount_ttc, 0, ',', ' ');
        $due    = \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y');
        $msg    = $customMsg ? "<p>{$customMsg}</p>" : '';

        return "
        <p>Bonjour,</p>
        {$msg}
        <p>Veuillez trouver ci-joint la facture <strong>{$invoice->invoice_number}</strong>
           d'un montant de <strong>{$amount} XAF TTC</strong>, échéance le <strong>{$due}</strong>.</p>
        <p>Cordialement,<br><strong>{$company->name}</strong></p>
        ";
    }
}
