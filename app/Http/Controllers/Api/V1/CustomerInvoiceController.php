<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Services\CameroonTaxEngine;
use App\Services\CryptographicInvoiceService;
use App\Services\JournalPostingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerInvoiceController extends Controller
{
    public function __construct(
        private CameroonTaxEngine $tax,
        private JournalPostingService $poster,
    ) {}

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
        // Plan limit: monthly invoice cap (free plan = 20/month).
        if (! app(\App\Services\PlanLimitService::class)->canCreateInvoice($company)) {
            return response()->json(
                app(\App\Services\PlanLimitService::class)->limitReached($company, 'factures/mois'),
                402
            );
        }

        $data = $request->validate([
            'customer_id'   => 'required|integer|exists:customers,id',
            'invoice_date'  => 'required|date',
            'due_date'      => 'required|date|after_or_equal:invoice_date',
            'amount_ht'     => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:1000',
        ]);

        // Credit limit enforcement
        $customer = Customer::findOrFail($data['customer_id']);
        if ($customer->credit_limit > 0) {
            $outstanding = CustomerInvoice::where('customer_id', $customer->id)
                ->whereIn('status', ['SENT', 'OVERDUE'])
                ->sum('amount_ttc');
            $taxed = $this->tax->compute($data['amount_ht']);
            if (($outstanding + $taxed['amount_ttc']) > $customer->credit_limit) {
                return response()->json([
                    'message' => "Credit limit exceeded. Outstanding: {$outstanding} XAF, Limit: {$customer->credit_limit} XAF.",
                ], 422);
            }
        } else {
            $taxed = $this->tax->compute($data['amount_ht']);
        }

        $invoice = CustomerInvoice::create([
            'company_id'     => $company->id,
            'customer_id'    => $data['customer_id'],
            'invoice_number' => 'CLI-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'invoice_date'   => $data['invoice_date'],
            'due_date'       => $data['due_date'],
            'amount_ht'      => $data['amount_ht'],
            'tva_amount'     => $taxed['base_vat'],
            'cac_amount'     => $taxed['cac'],
            'amount_ttc'     => $taxed['amount_ttc'],
            'status'         => 'DRAFT',
            'notes'          => $data['notes'] ?? null,
        ]);

        return response()->json($invoice->load('customer'), 201);
    }

    public function show(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);

        $invoice->load(['customer', 'journalEntry.lines.account']);

        // Credit notes issued against this invoice.
        $creditNotes = CustomerInvoice::where('credit_note_for_id', $invoice->id)
            ->get(['id', 'invoice_number', 'invoice_date', 'amount_ttc', 'status']);

        $data = $invoice->toArray();
        $data['is_overdue']   = $invoice->isOverdue();
        $data['credit_notes'] = $creditNotes;
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

    public function update(Request $request, Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'DRAFT', 422, 'Only DRAFT invoices can be edited.');

        $data = $request->validate([
            'customer_id'  => 'sometimes|integer|exists:customers,id',
            'invoice_date' => 'sometimes|date',
            'due_date'     => 'sometimes|date|after_or_equal:invoice_date',
            'amount_ht'    => 'sometimes|numeric|min:0',
            'notes'        => 'nullable|string|max:1000',
        ]);

        if (isset($data['amount_ht'])) {
            $taxed = $this->tax->compute($data['amount_ht']);
            $data['tva_amount']  = $taxed['base_vat'];
            $data['cac_amount']  = $taxed['cac'];
            $data['amount_ttc']  = $taxed['amount_ttc'];
        }

        $invoice->update($data);
        return response()->json($invoice->load('customer'));
    }

    public function pdf(Request $request, Company $company, CustomerInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        $invoice->loadMissing('customer');

        $crypto    = app(CryptographicInvoiceService::class);
        $timestamp = $invoice->created_at?->toIso8601String() ?? now()->toIso8601String();
        $hash      = $crypto->generateHash($company, (string) $invoice->amount_ttc, $timestamp);
        $qr        = $crypto->generateVerificationQr($hash, config('app.url'));

        $lang = $request->query('lang', 'FR');

        $pdf = Pdf::loadView('invoices.customer_invoice', [
            'company'       => $company,
            'invoice'       => $invoice,
            'client'        => [
                'name'    => $invoice->customer->name,
                'niu'     => $invoice->customer->niu ?? null,
                'address' => $invoice->customer->address ?? null,
            ],
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

        return $pdf->stream("facture-{$invoice->invoice_number}.pdf");
    }

    /**
     * GET /customer-invoices/{invoice}/receipt
     * Payment receipt (Reçu de paiement) — only for settled invoices.
     */
    public function receipt(Company $company, CustomerInvoice $invoice)
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_unless($invoice->status === 'PAID', 422, 'Un reçu n\'est disponible que pour une facture payée.');
        $invoice->loadMissing('customer');

        $receiptNumber = 'REC-' . date('Y', strtotime((string) $invoice->paid_at)) . '-' . str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('receipts.payment', [
            'company'       => $company,
            'invoice'       => $invoice,
            'customer'      => $invoice->customer,
            'receiptNumber' => $receiptNumber,
        ])->setPaper('a4');

        return $pdf->stream("recu-{$invoice->invoice_number}.pdf");
    }

    public function markSent(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'DRAFT', 422, 'Only DRAFT invoices can be sent.');
        $invoice->update(['status' => 'SENT']);

        // Email the client a copy (best-effort).
        $invoice->loadMissing('customer');
        if ($invoice->customer?->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($invoice->customer->email)->send(new \App\Mail\TransactionalMail(
                    subjectLine: "Facture N° {$invoice->invoice_number} de " . ($company->name ?? 'votre fournisseur'),
                    heading: "Vous avez reçu une facture",
                    lines: [
                        "Bonjour {$invoice->customer->name},",
                        "Veuillez trouver votre facture <strong>{$invoice->invoice_number}</strong> d'un montant de <strong>" . number_format($invoice->amount_ttc, 0, ',', ' ') . " XAF TTC</strong>, échéance le " . optional($invoice->due_date)->format('d/m/Y') . ".",
                    ],
                ));
            } catch (\Throwable $e) { /* ignore */ }
        }
        return response()->json($invoice);
    }

    public function markPaid(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if(!in_array($invoice->status, ['SENT', 'OVERDUE']), 422, 'Invoice is not outstanding.');
        $invoice->update(['status' => 'PAID', 'paid_at' => now()]);

        // Notify the company owner of the payment (best-effort).
        $owner = \App\Models\User::where('company_id', $company->id)->where('role', 'OWNER')->first();
        if ($owner?->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($owner->email)->send(new \App\Mail\TransactionalMail(
                    subjectLine: "Paiement reçu — " . number_format($invoice->amount_ttc, 0, ',', ' ') . " XAF",
                    heading: "✓ Paiement reçu",
                    lines: ["La facture <strong>{$invoice->invoice_number}</strong> a été marquée comme payée (" . number_format($invoice->amount_ttc, 0, ',', ' ') . " XAF)."],
                    cta: ['url' => url('/app?page=customer-invoices'), 'label' => 'Voir les factures'],
                ));
            } catch (\Throwable $e) { /* ignore */ }
        }
        return response()->json($invoice);
    }

    public function creditNote(Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if($invoice->status !== 'PAID', 422, 'Credit notes can only be issued on PAID invoices.');

        // Post reversal journal entry:
        // Dr 411100 (Clients)  — reverse receivable
        // Cr 701100 (Ventes)   — reverse revenue (HT)
        // Cr 443100 (TVA)      — reverse output VAT
        // Cr 448600 (CAC)      — reverse CAC
        // Signs are inverted from original invoice posting
        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => now()->toDateString(),
            'reference_id'    => 'CN-' . $invoice->invoice_number,
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Avoir sur facture {$invoice->invoice_number}",
            'posting_type'    => 'STANDARD',
        ], [
            ['account_code' => '701100', 'debit' => $invoice->amount_ht,  'credit' => 0],
            ['account_code' => '443100', 'debit' => $invoice->tva_amount, 'credit' => 0],
            ['account_code' => '448600', 'debit' => $invoice->cac_amount, 'credit' => 0],
            ['account_code' => '411100', 'debit' => 0, 'credit' => $invoice->amount_ttc],
        ]);

        $cn = CustomerInvoice::create([
            'company_id'        => $company->id,
            'customer_id'       => $invoice->customer_id,
            'journal_entry_id'  => $entry->id,
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

    // POST /companies/{company}/customer-invoices/{invoice}/record-withholding
    // Records customer-side withholding (précompte reçu) when a DGE/CIME buyer withholds 5.5%
    public function recordWithholding(Request $request, Company $company, CustomerInvoice $invoice): JsonResponse
    {
        abort_if($invoice->company_id !== $company->id, 404);
        abort_if(!in_array($invoice->status, ['SENT', 'OVERDUE', 'PARTIAL']), 422, 'Invoice must be outstanding.');

        $data = $request->validate([
            'withholding_received' => 'required|numeric|min:0',
            'payment_date'         => 'required|date',
        ]);

        $withholdingAmt = round((float) $data['withholding_received'], 2);
        $netReceivable  = round($invoice->amount_ttc - $withholdingAmt, 2);

        // Post GL entry: Dr 447200 (Créances Précompte) Cr 411000 (Clients)
        $this->poster->post([
            'company_id'   => $company->id,
            'entry_date'   => $data['payment_date'],
            'reference'    => 'PRC-' . $invoice->invoice_number,
            'description'  => "Précompte client reçu — facture {$invoice->invoice_number}",
            'posting_type' => 'STANDARD',
            'source'       => 'CUSTOMER_INVOICE',
        ], [
            ['account_code' => '447200', 'debit' => $withholdingAmt, 'credit' => 0],
            ['account_code' => '411000', 'debit' => 0,               'credit' => $withholdingAmt],
        ]);

        $invoice->update([
            'withholding_received' => $withholdingAmt,
            'net_receivable'       => $netReceivable,
        ]);

        return response()->json([
            'invoice'              => $invoice->fresh(),
            'withholding_received' => $withholdingAmt,
            'net_receivable'       => $netReceivable,
        ]);
    }
}
