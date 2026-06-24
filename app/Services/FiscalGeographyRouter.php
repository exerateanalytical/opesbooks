<?php

namespace App\Services;

use App\Models\Company;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * DGI Fiscal Geography Policy Router.
 *
 * Determines which compliance modules activate based on a company's
 * registered tax center tier:
 *   - DGE  (Direction des Grandes Entreprises)  → mandatory withholding on all suppliers
 *   - CIME (Centre des Impôts des Moyennes Entreprises) → mandatory withholding on suppliers
 *   - CDI  (Centre des Impôts) / CSPL           → withholding fields hidden
 */
class FiscalGeographyRouter
{
    private const LARGE_CENTER_PREFIXES = ['DGE', 'CIME'];

    public function requiresSupplierWithholding(Company $company): bool
    {
        foreach (self::LARGE_CENTER_PREFIXES as $prefix) {
            if (stripos($company->tax_center, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the withholding tax (Précompte) rate applicable for the company.
     * DGE/CIME companies apply a 5.5% withholding on supplier invoices by default
     * under Cameroonian Finance Law (subject to supplier tax status).
     */
    public function withholdingRate(Company $company): string
    {
        return $this->requiresSupplierWithholding($company) ? '0.055' : '0.000';
    }

    /**
     * Build the additional withholding journal line for a supplier payment (DGE/CIME only).
     * Debit 401100 (supplier reduced), Credit 447100 (withholding tax payable to DGI).
     */
    public function buildWithholdingLine(Company $company, string $supplierAmountHt): ?array
    {
        if (! $this->requiresSupplierWithholding($company)) {
            return null;
        }

        $withheld = (string) BigDecimal::of($supplierAmountHt)
            ->multipliedBy($this->withholdingRate($company))
            ->toScale(2, RoundingMode::HalfUp);

        return [
            'account_code' => '447100',
            'debit'        => '0.00',
            'credit'       => $withheld,
            'description'  => "Précompte retenu à la source ({$this->withholdingRate($company)}%) - {$company->tax_center}",
        ];
    }

    public function getActiveFiscalModules(Company $company): array
    {
        return [
            'withholding_tax_active' => $this->requiresSupplierWithholding($company),
            'withholding_rate'       => $this->withholdingRate($company),
            'prorata_active'         => (float) $company->vat_prorata_coefficient < 100.0,
            'prorata_coefficient'    => $company->vat_prorata_coefficient,
            'tax_center_tier'        => $this->resolveTier($company),
        ];
    }

    private function resolveTier(Company $company): string
    {
        foreach (self::LARGE_CENTER_PREFIXES as $prefix) {
            if (stripos($company->tax_center, $prefix) === 0) {
                return $prefix;
            }
        }
        return 'CDI';
    }
}
