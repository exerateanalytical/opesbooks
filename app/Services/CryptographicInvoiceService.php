<?php

namespace App\Services;

use App\Models\Company;
use App\Models\JournalEntry;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Generates immutable SHA-256 fingerprints for every finalized invoice.
 * The hash binds: company NIU + total TTC (string) + ISO timestamp.
 * A QR code embedding the verification URL is returned as a base64 PNG
 * for embedding in invoice PDF layouts.
 */
class CryptographicInvoiceService
{
    public function generateHash(Company $company, string $totalTtc, string $isoTimestamp): string
    {
        $payload = implode('|', [
            $company->niu,
            $totalTtc,
            $isoTimestamp,
        ]);

        return hash('sha256', $payload);
    }

    public function stampEntry(JournalEntry $entry, string $totalTtc): string
    {
        $hash = $this->generateHash(
            $entry->company,
            $totalTtc,
            $entry->created_at?->toIso8601String() ?? now()->toIso8601String()
        );

        $entry->update(['invoice_crypto_hash' => $hash]);

        return $hash;
    }

    /**
     * Returns a base64-encoded PNG QR code pointing to the verification endpoint.
     * The QR embeds the full verification URL so a DGI inspector can scan and confirm.
     */
    public function generateVerificationQr(string $hash, string $appUrl): string
    {
        $verificationUrl = rtrim($appUrl, '/') . '/api/v1/verify/invoice/' . $hash;

        $png = QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    public function verify(string $hash, Company $company, string $totalTtc, string $isoTimestamp): bool
    {
        return hash_equals($this->generateHash($company, $totalTtc, $isoTimestamp), $hash);
    }
}
