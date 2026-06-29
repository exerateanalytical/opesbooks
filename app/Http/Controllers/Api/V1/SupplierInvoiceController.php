<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\CryptographicInvoiceService;
use App\Services\SupplierInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'due_date'          => 'required|date',
            'amount_ht'         => 'required|numeric|min:0',
            'tva_amount'        => 'required|numeric|min:0',
            'cac_amount'        => 'nullable|numeric|min:0',
            'expense_account'   => 'required|string|size:6',
            'notes'             => 'nullable|string',
        ]);

        $amountTtc  = $data['amount_ht'] + $data['tva_amount'] + ($data['cac_amount'] ?? 0);

        // Atomic: if posting throws (e.g. ledger imbalance), the DRAFT is rolled
        // back rather than left orphaned.
        $invoice = \Illuminate\Support\Facades\DB::transaction(function () use ($company, $data, $amountTtc) {
            $invoice = SupplierInvoice::create([
                'company_id'     => $company->id,
                'supplier_id'    => $data['supplier_id'],
                'invoice_number' => $data['invoice_number'],
                'supplier_ref'   => $data['supplier_ref'] ?? null,
                'invoice_date'   => $data['invoice_date'],
                'due_date'       => $data['due_date'],
                'amount_ht'      => $data['amount_ht'],
                'tva_amount'     => $data['tva_amount'],
                'cac_amount'     => $data['cac_amount'] ?? 0,
                'amount_ttc'     => $amountTtc,
                'net_payable'    => $amountTtc,
                'status'         => 'DRAFT',
                'notes'          => $data['notes'] ?? null,
            ]);

            return $this->svc->post($invoice, $data['expense_account']);
        });

        return response()->json($invoice->load('supplier'), 201);
    }

    public function show(Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);

        $invoice->load(['supplier', 'journalEntry.lines.account']);
        $data = $invoice->toArray();
        $data['journal_lines'] = $invoice->journalEntry
            ? $invoice->journalEntry->lines->map(fn ($l) => [
                'account' => $l->account?->code,
                'label'   => $l->account?->label,
                'debit'   => (float) $l->debit,
                'credit'  => (float) $l->credit,
            ])
            : [];

        return response()->json($data);
    }

    public function update(Request $request, Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'DRAFT', 422, 'Only DRAFT invoices can be edited.');

        $data = $request->validate([
            'supplier_id'     => 'sometimes|exists:suppliers,id',
            'invoice_number'  => 'sometimes|string|max:100',
            'supplier_ref'    => 'nullable|string|max:100',
            'invoice_date'    => 'sometimes|date',
            'due_date'        => 'sometimes|date',
            'amount_ht'       => 'sometimes|numeric|min:0',
            'tva_amount'      => 'sometimes|numeric|min:0',
            'cac_amount'      => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        if (isset($data['amount_ht']) || isset($data['tva_amount']) || isset($data['cac_amount'])) {
            $ht  = $data['amount_ht']  ?? $invoice->amount_ht;
            $tva = $data['tva_amount'] ?? $invoice->tva_amount;
            $cac = $data['cac_amount'] ?? $invoice->cac_amount;
            $data['amount_ttc'] = $ht + $tva + $cac;
            $data['net_payable'] = $data['amount_ttc'];
        }

        $invoice->update($data);
        return response()->json($invoice->load('supplier'));
    }

    public function pdf(Request $request, Company $company, SupplierInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        $invoice->loadMissing('supplier');

        $crypto    = app(CryptographicInvoiceService::class);
        $timestamp = $invoice->created_at?->toIso8601String() ?? now()->toIso8601String();
        $hash      = $crypto->generateHash($company, (string) $invoice->amount_ttc, $timestamp);
        $qr        = $crypto->generateVerificationQr($hash, config('app.url'));

        $lang = $request->query('lang', 'FR');

        $pdf = Pdf::loadView('invoices.supplier_invoice', [
            'company'       => $company,
            'invoice'       => $invoice,
            'supplier'      => $invoice->supplier,
            'invoiceNumber' => $invoice->invoice_number,
            'invoiceDate'   => $invoice->invoice_date,
            'dueDate'       => $invoice->due_date,
            'amountHt'      => $invoice->amount_ht,
            'tvaAmount'     => $invoice->tva_amount,
            'cacAmount'     => $invoice->cac_amount,
            'amountTtc'     => $invoice->amount_ttc,
            'notes'         => $invoice->notes,
            'hash'          => $hash,
            'isoTimestamp'  => $timestamp,
            'qrBase64'      => $qr,
            'lang'          => $lang,
        ])->setPaper('a4');

        return $pdf->stream("facture-fournisseur-{$invoice->invoice_number}.pdf");
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
