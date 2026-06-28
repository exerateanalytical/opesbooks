<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\CustomerQuotation;
use App\Services\CameroonTaxEngine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerQuotationController extends Controller
{
    public function __construct(private CameroonTaxEngine $tax) {}

    // GET /companies/{company}/quotations
    public function index(Request $request, Company $company)
    {
        return response()->json(
            CustomerQuotation::where('company_id', $company->id)
                ->with('customer:id,name')
                ->when($request->status,      fn($q, $v) => $q->where('status', $v))
                ->when($request->customer_id, fn($q, $v) => $q->where('customer_id', $v))
                ->orderByDesc('quotation_date')
                ->paginate(25)
        );
    }

    // POST /companies/{company}/quotations
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'customer_id'              => 'required|exists:customers,id',
            'quotation_date'           => 'required|date',
            'valid_until'              => 'nullable|date|after_or_equal:quotation_date',
            'notes'                    => 'nullable|string',
            'lines'                    => 'nullable|array',
            'lines.*.description'      => 'required_with:lines|string|max:255',
            'lines.*.account_code'     => 'nullable|string|max:10',
            'lines.*.quantity'         => 'required_with:lines|numeric|min:0.0001',
            'lines.*.unit_price_ht'    => 'required_with:lines|numeric|min:0',
        ]);

        $prefix = 'DEV-' . date('Ym') . '-';
        $last   = CustomerQuotation::where('quotation_number', 'like', $prefix . '%')->count();
        $number = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        $amountHt = 0;
        $lineData = [];
        foreach ($data['lines'] ?? [] as $l) {
            $total     = round($l['quantity'] * $l['unit_price_ht'], 2);
            $amountHt += $total;
            $lineData[] = ['description' => $l['description'], 'account_code' => $l['account_code'] ?? null, 'quantity' => $l['quantity'], 'unit_price_ht' => $l['unit_price_ht'], 'line_total_ht' => $total];
        }
        $tva       = round($amountHt * 0.175, 2);
        $cac       = round($tva * 0.10, 2);
        $amountTtc = round($amountHt + $tva + $cac, 2);

        $quotation = DB::transaction(function () use ($company, $data, $number, $amountHt, $tva, $cac, $amountTtc, $lineData) {
            $q = CustomerQuotation::create([
                'company_id'       => $company->id,
                'customer_id'      => $data['customer_id'],
                'quotation_number' => $number,
                'quotation_date'   => $data['quotation_date'],
                'valid_until'      => $data['valid_until'] ?? null,
                'amount_ht'        => $amountHt,
                'tva_amount'       => $tva,
                'cac_amount'       => $cac,
                'amount_ttc'       => $amountTtc,
                'status'           => 'DRAFT',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($lineData as $line) {
                $q->lines()->create($line);
            }

            return $q;
        });

        return response()->json($quotation->load('lines', 'customer:id,name'), 201);
    }

    // GET /companies/{company}/quotations/{quotation}
    public function show(Company $company, CustomerQuotation $quotation)
    {
        abort_if($quotation->company_id !== $company->id, 404);
        return response()->json($quotation->load('lines', 'customer:id,name,email,phone', 'convertedInvoice:id,invoice_number'));
    }

    // PUT /companies/{company}/quotations/{quotation}/status
    public function updateStatus(Request $request, Company $company, CustomerQuotation $quotation)
    {
        abort_if($quotation->company_id !== $company->id, 404);
        $data = $request->validate(['status' => 'required|in:DRAFT,SENT,ACCEPTED,REJECTED']);
        $quotation->update(['status' => $data['status']]);
        return response()->json($quotation);
    }

    // POST /companies/{company}/quotations/{quotation}/convert
    // Convert accepted quotation → customer invoice
    public function convert(Request $request, Company $company, CustomerQuotation $quotation)
    {
        abort_if($quotation->company_id !== $company->id, 404);
        abort_if($quotation->status === 'CONVERTED', 422, 'Already converted.');
        abort_if($quotation->status === 'REJECTED',  422, 'Rejected quotations cannot be converted.');

        $data = $request->validate([
            'invoice_date' => 'required|date',
            'due_date'     => 'required|date|after_or_equal:invoice_date',
        ]);

        $prefix = 'FAC-' . date('Ym') . '-';
        $last   = CustomerInvoice::where('invoice_number', 'like', $prefix . '%')
                      ->where('company_id', $company->id)->count();
        $invNumber = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        $invoice = DB::transaction(function () use ($company, $quotation, $data, $invNumber) {
            $inv = CustomerInvoice::create([
                'company_id'     => $company->id,
                'customer_id'    => $quotation->customer_id,
                'invoice_number' => $invNumber,
                'invoice_date'   => $data['invoice_date'],
                'due_date'       => $data['due_date'],
                'amount_ht'      => $quotation->amount_ht,
                'tva_amount'     => $quotation->tva_amount,
                'cac_amount'     => $quotation->cac_amount,
                'amount_ttc'     => $quotation->amount_ttc,
                'status'         => 'SENT',
            ]);

            $quotation->update(['status' => 'CONVERTED', 'converted_invoice_id' => $inv->id]);
            return $inv;
        });

        return response()->json(['quotation' => $quotation->fresh(), 'invoice' => $invoice], 201);
    }

    // DELETE /companies/{company}/quotations/{quotation}
    public function destroy(Company $company, CustomerQuotation $quotation)
    {
        abort_if($quotation->company_id !== $company->id, 404);
        abort_if($quotation->status === 'CONVERTED', 422, 'Cannot delete a converted quotation.');
        $quotation->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // GET /companies/{company}/quotations/{quotation}/pdf
    public function pdf(Company $company, CustomerQuotation $quotation)
    {
        abort_if($quotation->company_id !== $company->id, 404);
        $quotation->load('lines');
        $customer = \App\Models\Customer::find($quotation->customer_id);

        $pdf = Pdf::loadView('quotations.quotation', [
            'company'   => $company,
            'quotation' => $quotation,
            'customer'  => $customer,
            'lines'     => $quotation->lines,
        ])->setPaper('a4');

        return $pdf->stream("DEV-{$quotation->quotation_number}.pdf");
    }
}
