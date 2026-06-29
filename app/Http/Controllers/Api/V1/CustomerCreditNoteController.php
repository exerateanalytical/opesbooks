<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerCreditNote;
use App\Models\CustomerInvoice;
use App\Services\CameroonTaxEngine;
use App\Services\JournalPostingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerCreditNoteController extends Controller
{
    public function __construct(
        private JournalPostingService $posting,
        private CameroonTaxEngine $tax,
    ) {}

    // GET /companies/{company}/customers/{customer}/credit-notes
    public function index(Company $company, Customer $customer)
    {
        abort_if($customer->company_id !== $company->id, 404);

        return response()->json(
            CustomerCreditNote::where('company_id', $company->id)
                ->where('customer_id', $customer->id)
                ->with('originalInvoice:id,invoice_number')
                ->orderByDesc('credit_note_date')
                ->get()
        );
    }

    // POST /companies/{company}/customers/{customer}/credit-notes
    public function store(Request $request, Company $company, Customer $customer)
    {
        abort_if($customer->company_id !== $company->id, 404);

        $data = $request->validate([
            'original_invoice_id' => 'nullable|exists:customer_invoices,id',
            'credit_note_date'    => 'required|date',
            'reason'              => 'nullable|string',
            'amount_ht'           => 'required|numeric|min:0',
        ]);

        $amountHt  = (float) $data['amount_ht'];
        $tva       = round($amountHt * 0.175, 2);
        $cac       = round($tva * 0.10, 2);
        $amountTtc = round($amountHt + $tva + $cac, 2);

        $prefix = 'AV-C-' . date('Ym') . '-';
        $last   = CustomerCreditNote::where('credit_note_number', 'like', $prefix . '%')->count();
        $number = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        $entry = $this->posting->post([
            'company_id'      => $company->id,
            'posting_date'    => $data['credit_note_date'],
            'reference_id'    => $number,
            'memo'            => "Avoir client: {$customer->name}" . ($data['reason'] ? " — {$data['reason']}" : ''),
            'posting_type'    => 'ADJUSTMENT',
            'source_pipeline' => 'MANUAL_INVOICE',
        ], [
            // Reverse the original sale: Dr Revenue / Cr Customer
            ['account_code' => '701000', 'debit' => $amountHt,  'credit' => 0],
            ['account_code' => '443100', 'debit' => $tva,        'credit' => 0],
            ['account_code' => '448600', 'debit' => $cac,        'credit' => 0],
            ['account_code' => '411000', 'debit' => 0,           'credit' => $amountTtc],
        ]);

        $cn = CustomerCreditNote::create([
            'company_id'          => $company->id,
            'customer_id'         => $customer->id,
            'original_invoice_id' => $data['original_invoice_id'] ?? null,
            'credit_note_number'  => $number,
            'credit_note_date'    => $data['credit_note_date'],
            'reason'              => $data['reason'] ?? null,
            'amount_ht'           => $amountHt,
            'tva_amount'          => $tva,
            'cac_amount'          => $cac,
            'amount_ttc'          => $amountTtc,
            'status'              => 'ISSUED',
            'journal_entry_id'    => $entry->id,
        ]);

        return response()->json($cn, 201);
    }

    // GET /companies/{company}/customers/{customer}/credit-notes/{cn}
    public function show(Company $company, Customer $customer, CustomerCreditNote $creditNote)
    {
        abort_if($creditNote->company_id !== $company->id, 404);
        return response()->json($creditNote->load('originalInvoice:id,invoice_number', 'customer:id,name'));
    }

    // GET /companies/{company}/customers/{customer}/credit-notes/{cn}/pdf
    public function pdf(Company $company, Customer $customer, CustomerCreditNote $creditNote)
    {
        abort_if($creditNote->company_id !== $company->id, 404);
        $originalInvoice = $creditNote->original_invoice_id
            ? CustomerInvoice::find($creditNote->original_invoice_id)
            : null;

        $pdf = Pdf::loadView('credit_notes.customer', [
            'company'         => $company,
            'cn'              => $creditNote,
            'customer'        => $customer,
            'originalInvoice' => $originalInvoice,
        ])->setPaper('a4');

        return $pdf->stream("AV-{$creditNote->credit_note_number}.pdf");
    }
}
