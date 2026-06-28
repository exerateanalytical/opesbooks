<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierCreditNote;
use App\Models\SupplierInvoice;
use App\Services\FiscalGeographyRouter;
use App\Services\JournalPostingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SupplierCreditNoteController extends Controller
{
    public function __construct(
        private JournalPostingService $posting,
        private FiscalGeographyRouter $router,
    ) {}

    // GET /companies/{company}/suppliers/{supplier}/credit-notes
    public function index(Company $company, Supplier $supplier)
    {
        abort_if($supplier->company_id !== $company->id, 404);

        return response()->json(
            SupplierCreditNote::where('company_id', $company->id)
                ->where('supplier_id', $supplier->id)
                ->with('originalInvoice:id,invoice_number')
                ->orderByDesc('credit_note_date')
                ->get()
        );
    }

    // POST /companies/{company}/suppliers/{supplier}/credit-notes
    public function store(Request $request, Company $company, Supplier $supplier)
    {
        abort_if($supplier->company_id !== $company->id, 404);

        $data = $request->validate([
            'original_invoice_id' => 'nullable|exists:supplier_invoices,id',
            'credit_note_date'    => 'required|date',
            'reason'              => 'nullable|string',
            'amount_ht'           => 'required|numeric|min:0',
        ]);

        $amountHt  = (float) $data['amount_ht'];
        $tva       = round($amountHt * 0.175, 2);
        $netPayable = round($amountHt + $tva, 2);

        $prefix = 'AV-F-' . date('Ym') . '-';
        $last   = SupplierCreditNote::where('credit_note_number', 'like', $prefix . '%')->count();
        $number = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        // Reverse purchase: Dr Supplier / Cr Expense + TVA
        $lines = [
            ['account_code' => '401000', 'debit' => $netPayable, 'credit' => 0],
            ['account_code' => '601000', 'debit' => 0,           'credit' => $amountHt],
            ['account_code' => '445200', 'debit' => 0,           'credit' => $tva],
        ];

        // Reverse withholding if applicable
        $withholdingLine = $this->router->buildWithholdingLine($company, (string) $amountHt);
        if ($withholdingLine) {
            $wAmt = (float) $withholdingLine['credit'];
            $lines[] = ['account_code' => '447100', 'debit' => 0,    'credit' => -$wAmt]; // reverse
            $lines[] = ['account_code' => '401000', 'debit' => 0,    'credit' => $wAmt];  // reduce payable
        }

        $entry = $this->posting->post([
            'company_id'   => $company->id,
            'entry_date'   => $data['credit_note_date'],
            'reference'    => $number,
            'description'  => "Avoir fournisseur: {$supplier->name}" . ($data['reason'] ? " — {$data['reason']}" : ''),
            'posting_type' => 'ADJUSTMENT',
            'source'       => 'CREDIT_NOTE',
        ], $lines);

        $cn = SupplierCreditNote::create([
            'company_id'          => $company->id,
            'supplier_id'         => $supplier->id,
            'original_invoice_id' => $data['original_invoice_id'] ?? null,
            'credit_note_number'  => $number,
            'credit_note_date'    => $data['credit_note_date'],
            'reason'              => $data['reason'] ?? null,
            'amount_ht'           => $amountHt,
            'tva_amount'          => $tva,
            'net_payable'         => $netPayable,
            'status'              => 'RECEIVED',
            'journal_entry_id'    => $entry->id,
        ]);

        return response()->json($cn, 201);
    }

    // GET /companies/{company}/suppliers/{supplier}/credit-notes/{cn}
    public function show(Company $company, Supplier $supplier, SupplierCreditNote $creditNote)
    {
        abort_if($creditNote->company_id !== $company->id, 404);
        return response()->json($creditNote->load('originalInvoice:id,invoice_number', 'supplier:id,name'));
    }

    // GET /companies/{company}/suppliers/{supplier}/credit-notes/{cn}/pdf
    public function pdf(Company $company, Supplier $supplier, SupplierCreditNote $creditNote)
    {
        abort_if($creditNote->company_id !== $company->id, 404);
        $originalInvoice = $creditNote->original_invoice_id
            ? SupplierInvoice::find($creditNote->original_invoice_id)
            : null;

        $pdf = Pdf::loadView('credit_notes.supplier', [
            'company'         => $company,
            'cn'              => $creditNote,
            'supplier'        => $supplier,
            'originalInvoice' => $originalInvoice,
        ])->setPaper('a4');

        return $pdf->stream("AV-{$creditNote->credit_note_number}.pdf");
    }
}
