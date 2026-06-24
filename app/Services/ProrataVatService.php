<?php

namespace App\Services;

use App\Models\Company;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * Partial VAT Recovery Coefficient engine (Prorata Scheme).
 *
 * For mixed-activity companies (e.g., taxable + exempt operations),
 * only a fraction of input VAT is recoverable from the DGI.
 * The non-recoverable portion is re-absorbed into the Class 6 expense value.
 *
 * Example: Prorata = 80%
 *   Recoverable VAT  → 80% → Account 445200 (debit)
 *   Non-recoverable  → 20% → added back into the Class 6 expense debit line
 */
class ProrataVatService
{
    private const SCALE = 2;

    public function splitInputVat(Company $company, string $baseVatAmount): array
    {
        $vat      = BigDecimal::of($baseVatAmount);
        $prorata  = BigDecimal::of($company->prorataMultiplier());

        $recoverable    = $vat->multipliedBy($prorata)->toScale(self::SCALE, RoundingMode::HalfUp);
        $nonRecoverable = $vat->minus($recoverable)->toScale(self::SCALE, RoundingMode::HalfUp);

        return [
            'recoverable_vat'     => (string) $recoverable,
            'non_recoverable_vat' => (string) $nonRecoverable,
            'prorata_coefficient' => (string) $company->vat_prorata_coefficient,
        ];
    }

    /**
     * Build journal lines for a purchase with prorata VAT split.
     * Returns lines ready for JournalPostingService::post().
     *
     * @param string $amountHt     Expense amount before tax
     * @param string $baseVat      Full 17.5% VAT on the amount HT
     * @param string $expenseAccount  Class 6 account code (e.g. '601100')
     * @param string $creditAccount   Payment account (e.g. '401100' or '571100')
     */
    public function buildPurchaseLines(
        Company $company,
        string $amountHt,
        string $baseVat,
        string $expenseAccount,
        string $creditAccount
    ): array {
        $split = $this->splitInputVat($company, $baseVat);

        $expenseDebit = BigDecimal::of($amountHt)
            ->plus(BigDecimal::of($split['non_recoverable_vat']))
            ->toScale(self::SCALE, RoundingMode::HalfUp);

        $totalCredit = BigDecimal::of($amountHt)
            ->plus(BigDecimal::of($baseVat))
            ->toScale(self::SCALE, RoundingMode::HalfUp);

        $lines = [
            [
                'account_code' => $expenseAccount,
                'debit'        => (string) $expenseDebit,
                'credit'       => '0.00',
                'description'  => "Expense HT + Non-recoverable VAT ({$split['non_recoverable_vat']} XAF absorbed)",
            ],
        ];

        if (BigDecimal::of($split['recoverable_vat'])->isGreaterThan(BigDecimal::zero())) {
            $lines[] = [
                'account_code' => '445200',
                'debit'        => $split['recoverable_vat'],
                'credit'       => '0.00',
                'description'  => "TVA Récupérable ({$company->vat_prorata_coefficient}% prorata)",
            ];
        }

        $lines[] = [
            'account_code' => $creditAccount,
            'debit'        => '0.00',
            'credit'       => (string) $totalCredit,
            'description'  => 'Fournisseur / Caisse créditée',
        ];

        return $lines;
    }
}
