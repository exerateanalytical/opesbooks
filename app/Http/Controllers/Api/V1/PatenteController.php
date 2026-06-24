<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PatenteRecord;
use App\Services\JournalPostingService;
use Illuminate\Http\Request;

/**
 * Patente — Cameroonian local business licence tax.
 * Tracked per tax year, posted to GL account 645100 (Patentes et licences).
 */
class PatenteController extends Controller
{
    public function __construct(private JournalPostingService $posting) {}

    // GET /companies/{company}/patente
    public function index(Company $company)
    {
        return response()->json(PatenteRecord::where('company_id', $company->id)->orderByDesc('tax_year')->get());
    }

    // POST /companies/{company}/patente
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'tax_year'        => 'required|integer|min:2020|max:2099',
            'patente_number'  => 'nullable|string|max:100',
            'amount_due_xaf'  => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        abort_if(
            PatenteRecord::where('company_id', $company->id)->where('tax_year', $data['tax_year'])->exists(),
            422,
            "Patente for year {$data['tax_year']} already exists."
        );

        $record = PatenteRecord::create([
            'company_id'      => $company->id,
            'tax_year'        => $data['tax_year'],
            'patente_number'  => $data['patente_number'] ?? null,
            'amount_due_xaf'  => $data['amount_due_xaf'],
            'amount_paid_xaf' => 0,
            'due_date'        => $data['due_date'] ?? null,
            'status'          => 'PENDING',
            'notes'           => $data['notes'] ?? null,
        ]);

        return response()->json($record, 201);
    }

    // POST /companies/{company}/patente/{record}/pay
    public function pay(Request $request, Company $company, PatenteRecord $patenteRecord)
    {
        abort_if($patenteRecord->company_id !== $company->id, 404);

        $data = $request->validate([
            'amount_paid_xaf' => 'required|numeric|min:1',
            'paid_date'       => 'required|date',
            'payment_account' => 'nullable|string|max:10',  // default 571100
        ]);

        $paymentAccount = $data['payment_account'] ?? '571100';
        $amount         = (float) $data['amount_paid_xaf'];

        $entry = $this->posting->post([
            'company_id'   => $company->id,
            'entry_date'   => $data['paid_date'],
            'reference'    => "PATENTE-{$patenteRecord->tax_year}",
            'description'  => "Paiement patente {$patenteRecord->tax_year}",
            'posting_type' => 'STANDARD',
            'source'       => 'PATENTE',
        ], [
            ['account_code' => '645100', 'debit' => $amount, 'credit' => 0],
            ['account_code' => $paymentAccount, 'debit' => 0, 'credit' => $amount],
        ]);

        $totalPaid = $patenteRecord->amount_paid_xaf + $amount;
        $patenteRecord->update([
            'amount_paid_xaf' => $totalPaid,
            'paid_date'       => $data['paid_date'],
            'status'          => $totalPaid >= $patenteRecord->amount_due_xaf ? 'PAID' : 'PENDING',
            'journal_entry_id'=> $entry->id,
        ]);

        return response()->json($patenteRecord->fresh());
    }

    // DELETE /companies/{company}/patente/{record}
    public function destroy(Company $company, PatenteRecord $patenteRecord)
    {
        abort_if($patenteRecord->company_id !== $company->id, 404);
        abort_if($patenteRecord->status === 'PAID', 422, 'Cannot delete a paid patente record.');
        $patenteRecord->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
