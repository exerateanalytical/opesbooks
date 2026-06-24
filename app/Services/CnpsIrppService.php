<?php

namespace App\Services;

/**
 * Cameroonian payroll tax engine.
 * Sources: Loi des Finances 2024, Décret CNPS, Code Général des Impôts.
 */
class CnpsIrppService
{
    // CNPS rates
    private const CNPS_EMPLOYEE_RATE    = 0.028;   // 2.8%
    private const CNPS_EMPLOYEE_CEILING = 750_000;  // XAF/month cap
    private const CNPS_EMPLOYER_RATE    = 0.112;    // 11.2% (vieillesse 4.2% + AT 1% + AF 7%)
    private const CNPS_EMPLOYER_CEILING = 750_000;

    // RAV (Redevance Audio-Visuelle)
    private const RAV_ANNUAL = 7_500;

    // IRPP progressive brackets (annual net taxable income in XAF)
    private const IRPP_BRACKETS = [
        ['min' =>         0, 'max' => 2_000_000, 'rate' => 0.10],
        ['min' => 2_000_001, 'max' => 3_000_000, 'rate' => 0.15],
        ['min' => 3_000_001, 'max' => 5_000_000, 'rate' => 0.25],
        ['min' => 5_000_001, 'max' => PHP_INT_MAX,'rate' => 0.35],
    ];

    private const CAC_RATE = 0.10; // CAC on IRPP

    /**
     * Calculate all payroll deductions for one employee for one month.
     *
     * @return array{cnps_employee, cnps_employer, irpp, cac_irpp, rav, net_salary}
     */
    public function calculate(float $grossMonthly): array
    {
        // CNPS employee
        $cnpsEmployee = round(min($grossMonthly, self::CNPS_EMPLOYEE_CEILING) * self::CNPS_EMPLOYEE_RATE, 0);

        // CNPS employer
        $cnpsEmployer = round(min($grossMonthly, self::CNPS_EMPLOYER_CEILING) * self::CNPS_EMPLOYER_RATE, 0);

        // IRPP — compute on annual basis then divide by 12
        $annualGross        = $grossMonthly * 12;
        $annualCnpsEmployee = $cnpsEmployee * 12;
        $annualNetTaxable   = ($annualGross - $annualCnpsEmployee) * 0.70; // 30% abattement frais pro
        $annualIrpp         = $this->computeIrpp($annualNetTaxable);
        $monthlyIrpp        = round($annualIrpp / 12, 0);

        // CAC on IRPP
        $cacIrpp = round($monthlyIrpp * self::CAC_RATE, 0);

        // RAV
        $rav = round(self::RAV_ANNUAL / 12, 0);

        // Net salary
        $net = $grossMonthly - $cnpsEmployee - $monthlyIrpp - $cacIrpp - $rav;

        return [
            'cnps_employee' => $cnpsEmployee,
            'cnps_employer' => $cnpsEmployer,
            'irpp'          => $monthlyIrpp,
            'cac_irpp'      => $cacIrpp,
            'rav'           => $rav,
            'net_salary'    => round(max($net, 0), 0),
        ];
    }

    private function computeIrpp(float $annualIncome): float
    {
        if ($annualIncome <= 0) return 0;
        $tax = 0.0;
        foreach (self::IRPP_BRACKETS as $bracket) {
            if ($annualIncome <= $bracket['min']) break;
            $taxable = min($annualIncome, $bracket['max']) - $bracket['min'] + 1;
            $tax += $taxable * $bracket['rate'];
        }
        return round($tax, 0);
    }
}
