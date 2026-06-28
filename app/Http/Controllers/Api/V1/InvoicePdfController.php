<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Services\CameroonTaxEngine;
use App\Services\CryptographicInvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class InvoicePdfController extends Controller
{
    public function __construct(private CryptographicInvoiceService $crypto) {}

    /**
     * POST /api/v1/companies/{company}/invoice/generate
     *
     * Generates a DGI-compliant SYSCOHADA invoice PDF with:
     *  - SHA-256 tamper-proof hash
     *  - QR code for DGI verification scan
     *  - Full TVA/CAC breakdown
     *  - Bilingual EN/FR layout
     */
    public function generate(Request $request, Company $company): Response
    {
        if (! $company->hasValidFiscalProfile()) {
            throw ValidationException::withMessages([
                'company' => ['Company fiscal profile is incomplete (NIU, RCCM, or Tax Center missing).'],
            ]);
        }

        $data = $request->validate([
            'invoice_number'     => 'required|string|max:50',
            'invoice_date'       => 'required|date_format:Y-m-d',
            'due_date'           => 'nullable|date_format:Y-m-d',
            'language'           => 'sometimes|in:FR,EN',
            'client_name'        => 'required|string|max:255',
            'client_niu'         => 'nullable|string|max:20',
            'client_address'     => 'nullable|string|max:500',
            'lines'              => 'required|array|min:1',
            'lines.*.description'=> 'required|string|max:255',
            'lines.*.quantity'   => 'required|numeric|min:0.01',
            'lines.*.unit_price_ht' => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $lang = $data['language'] ?? 'FR';

        // Compute line totals and grand totals using BigDecimal
        $invoiceLines  = [];
        $grandTotalHt  = BigDecimal::of('0');

        foreach ($data['lines'] as $line) {
            $qty       = BigDecimal::of((string) $line['quantity']);
            $unitPrice = BigDecimal::of((string) $line['unit_price_ht']);
            $lineHt    = $qty->multipliedBy($unitPrice)->toScale(2, RoundingMode::HalfUp);

            $tax = CameroonTaxEngine::compute($lineHt->__toString());

            $invoiceLines[] = [
                'description'   => $line['description'],
                'quantity'      => $qty->toScale(2, RoundingMode::HalfUp)->__toString(),
                'unit_price_ht' => $unitPrice->toScale(2, RoundingMode::HalfUp)->__toString(),
                'total_ht'      => $tax['amount_ht'],
                'tva'           => $tax['base_vat'],
                'cac'           => $tax['cac'],
                'total_ttc'     => $tax['amount_ttc'],
            ];

            $grandTotalHt = $grandTotalHt->plus(BigDecimal::of($tax['amount_ht']));
        }

        $grandTax    = CameroonTaxEngine::compute($grandTotalHt->toScale(2, RoundingMode::HalfUp)->__toString());
        $isoTimestamp = now()->toIso8601String();

        // Generate cryptographic hash and stamp the entry if journal_entry_id provided
        $hash = $this->crypto->generateHash($company, $grandTax['amount_ttc'], $isoTimestamp);
        $qrBase64 = $this->crypto->generateVerificationQr($hash, config('app.url'));

        // Stamp the journal entry if referenced
        if ($request->filled('journal_entry_id')) {
            $entry = JournalEntry::where('company_id', $company->id)
                ->findOrFail($request->input('journal_entry_id'));
            $entry->update(['invoice_crypto_hash' => $hash]);
        }

        $pdf = Pdf::loadView('invoices.invoice', [
            'company'       => $company,
            'client'        => [
                'name'    => $data['client_name'],
                'niu'     => $data['client_niu'] ?? null,
                'address' => $data['client_address'] ?? null,
            ],
            'invoiceNumber' => $data['invoice_number'],
            'invoiceDate'   => $data['invoice_date'],
            'dueDate'       => $data['due_date'] ?? null,
            'lines'         => $invoiceLines,
            'totals'        => $grandTax,
            'hash'          => $hash,
            'qrBase64'      => $qrBase64,
            'isoTimestamp'  => $isoTimestamp,
            'notes'         => $data['notes'] ?? null,
            'lang'          => $lang,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'OPES-INV-' . $data['invoice_number'] . '-' . date('Ymd') . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * GET /api/v1/companies/{company}/invoice/{entry}/download
     * Re-generates and downloads a PDF for a previously posted journal entry.
     */
    public function download(Request $request, Company $company, JournalEntry $entry): Response
    {
        abort_unless($entry->company_id === $company->id, 404);

        $lang = $request->query('lang', 'FR');

        // Reconstruct totals from journal lines
        $revenue = $entry->lines()
            ->whereHas('account', fn ($q) => $q->whereIn('code', ['701100', '706000']))
            ->sum('credit');

        $tax = CameroonTaxEngine::compute((string) $revenue);
        $hash = $entry->invoice_crypto_hash
            ?? $this->crypto->generateHash($company, $tax['amount_ttc'], $entry->created_at->toIso8601String());

        $qrBase64 = $this->crypto->generateVerificationQr($hash, config('app.url'));

        $pdf = Pdf::loadView('invoices.invoice', [
            'company'       => $company,
            'client'        => ['name' => 'Client', 'niu' => null, 'address' => null],
            'invoiceNumber' => $entry->reference_id,
            'invoiceDate'   => $entry->posting_date,
            'dueDate'       => null,
            'lines'         => [[
                'description'   => $entry->memo,
                'quantity'      => '1.00',
                'unit_price_ht' => $tax['amount_ht'],
                'total_ht'      => $tax['amount_ht'],
                'tva'           => $tax['base_vat'],
                'cac'           => $tax['cac'],
                'total_ttc'     => $tax['amount_ttc'],
            ]],
            'totals'        => $tax,
            'hash'          => $hash,
            'qrBase64'      => $qrBase64,
            'isoTimestamp'  => $entry->created_at->toIso8601String(),
            'notes'         => null,
            'lang'          => $lang,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('OPES-' . $entry->reference_id . '.pdf');
    }
}
