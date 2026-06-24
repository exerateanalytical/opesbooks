<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\SupplierInvoiceService;
use Illuminate\Http\Request;

class SupplierInvoiceController extends Controller
{
    public function __construct(private SupplierInvoiceService $svc) {}

    public function index(Request $request, Company $company)
    {
        $invoices = SupplierInvoice::where('company_id', $company->id)
            ->with('supplier')
            ->orderByDesc('invoice_date')
            ->paginate(50);

        return response()->json($invoices);
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'invoice_number'    => 'required|string|max:100',
            'supplier_ref'      => 'nullable|string|max:100',
            'invoice_date'      => 'required|date',
            'due_date'          => 'nullable|date',
            'amount_ht'         => 'required|numeric|min:0',
            'tva_amount'        => 'required|numeric|min:0',
            'cac_amount'        => 'nullable|numeric|min:0',
            'expense_account'   => 'required|string|size:6',
            'notes'             => 'nullable|string',
        ]);

        $amountTtc  = $data['amount_ht'] + $data['tva_amount'] + ($data['cac_amount'] ?? 0);

        $invoice = SupplierInvoice::create([
            'company_id'     => $company->id,
            'supplier_id'    => $data['supplier_id'],
            'invoice_number' => $data['invoice_number'],
            'supplier_ref'   => $data['supplier_ref'] ?? null,
            'invoice_date'   => $data['invoice_date'],
            'due_date'       => $data['due_date'] ?? null,
            'amount_ht'      => $data['amount_ht'],
            'tva_amount'     => $data['tva_amount'],
            'cac_amount'     => $data['cac_amount'] ?? 0,
            'amount_ttc'     => $amountTtc,
            'status'         => 'DRAFT',
            'notes'          => $data['notes'] ?? null,
        ]);

        $invoice = $this->svc->post($invoice, $data['expense_account']);

        return response()->json($invoice->load('supplier'), 201);
    }

    public function show(Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        return response()->json($invoice->load('supplier'));
    }

    public function pay(Request $request, Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status === 'PAID', 422, 'Invoice already paid.');

        $data = $request->validate([
            'payment_account' => 'required|string|size:6',
            'payment_ref'     => 'required|string|max:100',
        ]);

        $invoice = $this->svc->pay($invoice, $data['payment_account'], $data['payment_ref']);

        return response()->json($invoice->load('supplier'));
    }

    public function destroy(Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'DRAFT', 422, 'Only DRAFT invoices can be deleted.');
        $invoice->delete();
        return response()->json(['message' => 'Deleted.']);
    }
}
