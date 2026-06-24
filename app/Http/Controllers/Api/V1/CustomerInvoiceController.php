<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Services\CameroonTaxEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerInvoiceController extends Controller
{
    public function __construct(private CameroonTaxEngine $tax) {}

    public function index(Request $request, Company $company): JsonResponse
    {
        $invoices = CustomerInvoice::where('company_id', $company->id)
            ->with('customer:id,name')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('invoice_date')
            ->paginate(30);

        // Auto-mark overdue
        CustomerInvoice::where('company_id', $company->id)
            ->where('status', 'SENT')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'OVERDUE']);

        return response()->json($invoices);
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'customer_id'   => 'required|integer|exists:customers,id',
            'invoice_date'  => 'required|date',
            'due_date'      => 'required|date|after_or_equal:invoice_date',
            'amount_ht'     => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $taxed = $this->tax->fromHt($data['amount_ht']);
        $invoice = CustomerInvoice::create([
            'company_id'     => $company->id,
            'customer_id'    => $data['customer_id'],
            'invoice_number' => 'CLI-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'invoice_date'   => $data['invoice_date'],
            'due_date'       => $data['due_date'],
            'amount_ht'      => $data['amount_ht'],
            'tva_amount'     => $taxed['tva'],
            'cac_amount'     => $taxed['cac'],
            'amount_ttc'     => $taxed['ttc'],
            'status'         => 'DRAFT',
            'notes'          => $data['notes'] ?? null,
        ]);

        return response()->json($invoice->load('customer'), 201);
    }

    public function markSent(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'DRAFT', 422, 'Only DRAFT invoices can be sent.');
        $invoice->update(['status' => 'SENT']);
        return response()->json($invoice);
    }

    public function markPaid(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if(!in_array($invoice->status, ['SENT', 'OVERDUE']), 422, 'Invoice is not outstanding.');
        $invoice->update(['status' => 'PAID', 'paid_at' => now()]);
        return response()->json($invoice);
    }

    public function creditNote(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'PAID', 422, 'Credit notes can only be issued on PAID invoices.');

        $cn = CustomerInvoice::create([
            'company_id'        => $company->id,
            'customer_id'       => $invoice->customer_id,
            'invoice_number'    => 'CN-' . $invoice->invoice_number,
            'invoice_date'      => now()->toDateString(),
            'due_date'          => now()->toDateString(),
            'amount_ht'         => -$invoice->amount_ht,
            'tva_amount'        => -$invoice->tva_amount,
            'cac_amount'        => -$invoice->cac_amount,
            'amount_ttc'        => -$invoice->amount_ttc,
            'status'            => 'CREDIT_NOTE',
            'credit_note_for_id'=> $invoice->id,
            'notes'             => "Avoir sur facture {$invoice->invoice_number}",
        ]);

        return response()->json($cn->load('customer'), 201);
    }

    public function agedReceivables(Company $company): JsonResponse
    {
        return response()->json(
            app(\App\Services\FinancialStatementService::class)->agedReceivables($company)
        );
    }
}
