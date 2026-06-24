<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Support\Str;

class SupplierInvoiceService
{
    public function __construct(
        private JournalPostingService $poster,
        private FiscalGeographyRouter $router,
        private CameroonTaxEngine     $tax,
        private ProrataVatService     $prorata,
    ) {}

    /**
     * Post a supplier invoice to the general ledger.
     *
     * SYSCOHADA double-entry:
     *   Dr 601xxx / 605xxx / 621xxx  (expense — HT or HT + non-recoverable VAT)
     *   Dr 445200                    (recoverable TVA, prorata-adjusted)
     *   Cr 401100                    (supplier payable — TTC - withholding)
     *   Cr 447100                    (withholding payable — DGE/CIME only)
     */
    public function post(SupplierInvoice $invoice, string $expenseAccountCode): SupplierInvoice
    {
        $company  = $invoice->company;
        $supplier = $invoice->supplier;

        $amountHt  = (float) $invoice->amount_ht;
        $tvaAmount = (float) $invoice->tva_amount;

        // Prorata VAT split
        $prorataSplit = $this->prorata->splitInputVat($company, (string) $tvaAmount);
        $recoverableVat    = (float) $prorataSplit['recoverable_vat'];
        $nonRecoverableVat = (float) $prorataSplit['non_recoverable_vat'];

        // Withholding line (DGE/CIME only)
        $withholdingLine = $this->router->buildWithholdingLine($company, (string) $amountHt);
        $withholdingAmt  = $withholdingLine ? (float) $withholdingLine['credit'] : 0;

        $netPayable = $invoice->amount_ttc - $withholdingAmt;

        $lines = [
            // Expense (HT + non-recoverable VAT portion)
            ['account_code' => $expenseAccountCode, 'debit' => $amountHt + $nonRecoverableVat, 'credit' => 0],
        ];

        if ($recoverableVat > 0) {
            $lines[] = ['account_code' => '445200', 'debit' => $recoverableVat, 'credit' => 0];
        }

        $lines[] = ['account_code' => '401100', 'debit' => 0, 'credit' => $netPayable];

        if ($withholdingLine) {
            $lines[] = ['account_code' => '447100', 'debit' => 0, 'credit' => $withholdingAmt];
        }

        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => $invoice->invoice_date->toDateString(),
            'reference_id'    => $invoice->invoice_number,
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Facture fournisseur {$supplier->name} — {$invoice->invoice_number}",
            'posting_type'    => 'STANDARD',
        ], $lines);

        $invoice->update([
            'journal_entry_id' => $entry->id,
            'withholding_amount' => $withholdingAmt,
            'net_payable'        => $netPayable,
            'status'             => 'RECEIVED',
        ]);

        return $invoice->fresh();
    }

    /**
     * Mark supplier invoice as paid — post the payment journal entry.
     * Dr 401100 (supplier payable cleared)
     * Cr 521100 or 571xxx (bank / mobile money)
     */
    public function pay(SupplierInvoice $invoice, string $paymentAccountCode, string $paymentRef): SupplierInvoice
    {
        $company = $invoice->company;

        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => now()->toDateString(),
            'reference_id'    => 'PAY-' . $invoice->invoice_number,
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Règlement fournisseur {$invoice->supplier->name} — {$invoice->invoice_number}",
            'posting_type'    => 'STANDARD',
        ], [
            ['account_code' => '401100',            'debit' => $invoice->net_payable, 'credit' => 0],
            ['account_code' => $paymentAccountCode, 'debit' => 0, 'credit' => $invoice->net_payable],
        ]);

        $invoice->update([
            'status'      => 'PAID',
            'paid_at'     => now(),
            'payment_ref' => $paymentRef,
        ]);

        return $invoice->fresh();
    }
}
