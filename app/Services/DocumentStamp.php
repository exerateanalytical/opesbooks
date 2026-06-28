<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Stateless document stamp: a unique reference, generation timestamp, an
 * HMAC-signed verification token + QR, for ANY document. No DB row needed —
 * the verifier recomputes the HMAC from APP_KEY, so anyone scanning the QR can
 * confirm the document was issued by this company's OPESBooks instance.
 */
class DocumentStamp
{
    /**
     * @return array{ref:string, timestamp:string, verify_url:string, qr:string}
     */
    public function for(Company $company, string $type, ?string $naturalRef = null): array
    {
        $now = Carbon::now();
        $ref = $naturalRef ?: strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $type), 0, 3))
            . '-' . $company->id . '-' . $now->format('YmdHis');

        $payload = $this->b64url(json_encode([
            'c' => $company->niu,
            'n' => $company->name,
            't' => $type,
            'r' => $ref,
            'd' => $now->toIso8601String(),
        ]));
        $sig = $this->sign($payload);

        $verifyUrl = url('/verify?d=' . $payload . '&s=' . $sig);

        $svg = QrCode::format('svg')->size(110)->errorCorrection('M')->margin(0)->generate($verifyUrl);

        return [
            'ref'        => $ref,
            'timestamp'  => $now->format('d/m/Y H:i'),
            'verify_url' => $verifyUrl,
            'qr'         => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ];
    }

    /** Validate a (payload, signature) pair and return the decoded data, or null. */
    public function verify(?string $payload, ?string $sig): ?array
    {
        if (! $payload || ! $sig || ! hash_equals($this->sign($payload), $sig)) {
            return null;
        }
        $data = json_decode($this->b64urlDecode($payload), true);
        return is_array($data) ? $data : null;
    }

    private function sign(string $payload): string
    {
        return substr(hash_hmac('sha256', $payload, (string) config('app.key')), 0, 24);
    }

    private function b64url(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }

    private function b64urlDecode(string $s): string
    {
        return base64_decode(strtr($s, '-_', '+/')) ?: '';
    }
}
