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

    // IRPP standard annual lump-sum deduction (abattement forfaitaire), CGI art. 29.
    private const IRPP_ABATTEMENT = 500_000;

    // RAV (Redevance Audio-Visuelle) — progressive MONTHLY scale by base salary.
    // ⚠ Verify these amounts against the current Loi de Finances; the structure
    // (progressive lookup) is correct, the bracket figures are the published scale.
    // Each entry: monthly base salary up to `max` (XAF) pays `rav` per month.
    private const RAV_BRACKETS = [
        ['max' =>    50_000, 'rav' =>      0],
        ['max' =>   100_000, 'rav' =>    750],
        ['max' =>   200_000, 'rav' =>  1_950],
        ['max' =>   300_000, 'rav' =>  3_250],
        ['max' =>   400_000, 'rav' =>  4_550],
        ['max' =>   500_000, 'rav' =>  5_850],
        ['max' =>   600_000, 'rav' =>  7_150],
        ['max' =>   700_000, 'rav' =>  8_450],
        ['max' =>   800_000, 'rav' =>  9_750],
        ['max' =>   900_000, 'rav' => 11_050],
        ['max' => 1_000_000, 'rav' => 12_350],
        ['max' => PHP_INT_MAX, 'rav' => 13_000],
    ];

    // IRPP progressive brackets (annual net taxable income in XAF).
    // Bracket mins are contiguous with the previous max (no 1-XAF gap).
    private const IRPP_BRACKETS = [
        ['min' =>         0, 'max' => 2_000_000, 'rate' => 0.10],
        ['min' => 2_000_000, 'max' => 3_000_000, 'rate' => 0.15],
        ['min' => 3_000_000, 'max' => 5_000_000, 'rate' => 0.25],
        ['min' => 5_000_000, 'max' => PHP_INT_MAX,'rate' => 0.35],
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

        // IRPP — compute on annual basis then divide by 12.
        // Base = (gross − CNPS) − 30% frais professionnels − 500 000 abattement.
        $annualGross        = $grossMonthly * 12;
        $annualCnpsEmployee = $cnpsEmployee * 12;
        $annualNetTaxable   = max(0, ($annualGross - $annualCnpsEmployee) * 0.70 - self::IRPP_ABATTEMENT);
        $annualIrpp         = $this->computeIrpp($annualNetTaxable);
        $monthlyIrpp        = round($annualIrpp / 12, 0);

        // CAC on IRPP
        $cacIrpp = round($monthlyIrpp * self::CAC_RATE, 0);

        // RAV — progressive by monthly base salary.
        $rav = $this->ravFor($grossMonthly);

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

    /** Monthly RAV for a given monthly base salary (progressive lookup). */
    private function ravFor(float $grossMonthly): int
    {
        foreach (self::RAV_BRACKETS as $bracket) {
            if ($grossMonthly <= $bracket['max']) {
                return $bracket['rav'];
            }
        }
        return 0;
    }

    private function computeIrpp(float $annualIncome): float
    {
        if ($annualIncome <= 0) return 0;
        $tax = 0.0;
        foreach (self::IRPP_BRACKETS as $bracket) {
            if ($annualIncome <= $bracket['min']) break;
            $taxable = min($annualIncome, $bracket['max']) - $bracket['min'];
            $tax += $taxable * $bracket['rate'];
        }
        return round($tax, 0);
    }
}
