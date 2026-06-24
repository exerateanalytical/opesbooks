<?php

namespace App\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * Immutable tax computation engine for Cameroon DGI compliance.
 *
 * TVA rate: 17.5% (statutory)
 * CAC:      10% of TVA (Centimes Additionnels Communaux)
 * Effective combined rate: 19.25% on HT amount
 */
class CameroonTaxEngine
{
    private const TVA_RATE = '0.175';
    private const CAC_RATE = '0.10';
    private const SCALE    = 2;

    public static function compute(string $amountHt): array
    {
        $ht      = BigDecimal::of($amountHt);
        $baseVat = $ht->multipliedBy(self::TVA_RATE)->toScale(self::SCALE, RoundingMode::HalfUp);
        $cac     = $baseVat->multipliedBy(self::CAC_RATE)->toScale(self::SCALE, RoundingMode::HalfUp);
        $totalTax = $baseVat->plus($cac)->toScale(self::SCALE, RoundingMode::HalfUp);
        $ttc      = $ht->plus($totalTax)->toScale(self::SCALE, RoundingMode::HalfUp);

        return [
            'amount_ht'   => (string) $ht->toScale(self::SCALE, RoundingMode::HalfUp),
            'base_vat'    => (string) $baseVat,
            'cac'         => (string) $cac,
            'total_tax'   => (string) $totalTax,
            'amount_ttc'  => (string) $ttc,
            'vat_rate'    => self::TVA_RATE,
            'cac_rate'    => self::CAC_RATE,
        ];
    }

    public static function reverseFromTtc(string $amountTtc): array
    {
        // HT = TTC / 1.1925
        $ttc       = BigDecimal::of($amountTtc);
        $divisor   = BigDecimal::of('1')->plus(self::TVA_RATE)->plus(
            BigDecimal::of(self::TVA_RATE)->multipliedBy(self::CAC_RATE)
        );
        $ht = $ttc->dividedBy($divisor, self::SCALE + 4, RoundingMode::HalfUp);

        return self::compute((string) $ht->toScale(self::SCALE, RoundingMode::HalfUp));
    }
}
