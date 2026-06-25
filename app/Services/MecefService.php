<?php

namespace App\Services;

use App\Models\CustomerInvoice;
use App\Models\MecefConfig;
use App\Models\MecefLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * MECeF (Machine Électronique Certifiée de Facturation) — DGI Cameroun
 * e-invoice certification. The real DGI API is not publicly documented, so
 * this runs in sandbox mode by default: it mirrors the expected request/response
 * shape and produces a fiscal counter + QR payload, ready to point at the live
 * endpoint once credentials are issued.
 */
class MecefService
{
    public function __construct(private ?MecefConfig $config) {}

    public function certifyInvoice(CustomerInvoice $invoice): array
    {
        if (! $this->config || ! $this->config->is_active) {
            return ['success' => false, 'message' => 'MECeF non configuré pour cette entreprise.'];
        }

        $payload = $this->buildPayload($invoice);
        MecefLog::create([
            'company_id' => $invoice->company_id, 'invoice_id' => $invoice->id,
            'action' => 'certify_request', 'request_payload' => $payload, 'created_at' => now(),
        ]);

        $result = $this->config->sandbox_mode
            ? $this->simulate($invoice, $payload)
            : $this->callDgi($invoice, $payload);

        $invoice->update([
            'mecef_status'       => $result['success'] ? 'certified' : 'failed',
            'mecef_counter'      => $result['counter']  ?? null,
            'mecef_nim'          => $result['nim']      ?? $this->config->nim,
            'mecef_qr_data'      => $result['qr_data']  ?? null,
            'mecef_certified_at' => $result['success'] ? now() : null,
            'mecef_response_raw' => $result,
        ]);

        MecefLog::create([
            'company_id' => $invoice->company_id, 'invoice_id' => $invoice->id,
            'action' => $result['success'] ? 'certify_success' : 'certify_failed',
            'response_payload' => $result, 'http_status' => $result['http_status'] ?? ($result['success'] ? 200 : 500),
            'created_at' => now(),
        ]);

        return $result;
    }

    public function buildPayload(CustomerInvoice $invoice): array
    {
        return [
            'nim'          => $this->config->nim,
            'invoice_type' => 'FV',
            'invoice_ref'  => $invoice->invoice_number,
            'invoice_date' => optional($invoice->invoice_date)->format('Y-m-d'),
            'ht_amount'    => $invoice->amount_ht,
            'tva_amount'   => $invoice->tva_amount,
            'ttc_amount'   => $invoice->amount_ttc,
        ];
    }

    /** Sandbox: deterministic-ish fiscal counter + QR payload. */
    private function simulate(CustomerInvoice $invoice, array $payload): array
    {
        $seq     = CustomerInvoice::where('company_id', $invoice->company_id)
                    ->where('mecef_status', 'certified')->count() + 1;
        $counter = str_pad((string) $seq, 8, '0', STR_PAD_LEFT);
        $nim     = $this->config->nim ?: 'CM-MECeF-SANDBOX';
        $qr      = "{$nim}|{$invoice->invoice_number}|{$counter}|{$invoice->amount_ttc}|" . now()->format('YmdHis');

        return [
            'success'     => true,
            'sandbox'     => true,
            'counter'     => $counter,
            'nim'         => $nim,
            'qr_data'     => $qr,
            'certified_at'=> now()->toIso8601String(),
            'http_status' => 200,
        ];
    }

    /** Live DGI call (stub until the official endpoint/credentials are available). */
    private function callDgi(CustomerInvoice $invoice, array $payload): array
    {
        try {
            $res = Http::timeout(15)
                ->withToken((string) $this->config->api_token)
                ->post(rtrim((string) $this->config->api_endpoint, '/') . '/invoices/certify', $payload);

            if (! $res->successful()) {
                return ['success' => false, 'message' => 'DGI a refusé la requête.', 'http_status' => $res->status(), 'body' => $res->json()];
            }
            $body = $res->json();
            return [
                'success'     => true,
                'counter'     => $body['counter'] ?? $body['fiscal_counter'] ?? null,
                'nim'         => $body['nim'] ?? $this->config->nim,
                'qr_data'     => $body['qr'] ?? $body['qr_data'] ?? null,
                'http_status' => $res->status(),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Connexion DGI impossible: ' . $e->getMessage(), 'http_status' => 0];
        }
    }
}
