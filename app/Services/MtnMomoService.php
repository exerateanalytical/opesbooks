<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * MTN MoMo Collections (request-to-pay). Sandbox mode until real subscription
 * credentials are configured.
 */
class MtnMomoService
{
    public function isSandbox(): bool
    {
        return config('payment.mtn_momo.environment') !== 'live'
            || empty(config('payment.mtn_momo.subscription_key'));
    }

    public function requestPayment(array $data): array
    {
        $referenceId = (string) Str::uuid();

        if ($this->isSandbox()) {
            return ['success' => true, 'sandbox' => true, 'reference_id' => $referenceId, 'status' => 'SUCCESSFUL'];
        }

        try {
            $token = $this->accessToken();
            $res = Http::withToken($token)->withHeaders([
                'X-Reference-Id'            => $referenceId,
                'X-Target-Environment'      => config('payment.mtn_momo.environment'),
                'Ocp-Apim-Subscription-Key' => config('payment.mtn_momo.subscription_key'),
            ])->post(rtrim(config('payment.mtn_momo.base_url'), '/') . '/collection/v1_0/requesttopay', [
                'amount'       => (string) $data['amount'],
                'currency'     => 'XAF',
                'externalId'   => $data['order_id'],
                'payer'        => ['partyIdType' => 'MSISDN', 'partyId' => $data['phone']],
                'payerMessage' => 'Abonnement OPESBooks ' . ($data['plan_name'] ?? ''),
                'payeeNote'    => 'OPB-' . $data['order_id'],
            ]);
            return ['success' => $res->successful(), 'reference_id' => $referenceId, 'status' => $res->successful() ? 'PENDING' : 'FAILED'];
        } catch (\Throwable $e) {
            return ['success' => false, 'reference_id' => $referenceId, 'status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }

    public function getPaymentStatus(string $referenceId): array
    {
        if ($this->isSandbox()) {
            return ['status' => 'SUCCESSFUL', 'sandbox' => true];
        }
        try {
            $token = $this->accessToken();
            $res = Http::withToken($token)->withHeaders([
                'Ocp-Apim-Subscription-Key' => config('payment.mtn_momo.subscription_key'),
            ])->get(rtrim(config('payment.mtn_momo.base_url'), '/') . '/collection/v1_0/requesttopay/' . $referenceId);
            return $res->json() ?: ['status' => 'PENDING'];
        } catch (\Throwable $e) {
            return ['status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }

    private function accessToken(): ?string
    {
        return Cache::remember('mtn_momo_token', 3500, function () {
            $res = Http::withBasicAuth(config('payment.mtn_momo.api_user'), config('payment.mtn_momo.api_key'))
                ->withHeaders(['Ocp-Apim-Subscription-Key' => config('payment.mtn_momo.subscription_key')])
                ->post(rtrim(config('payment.mtn_momo.base_url'), '/') . '/collection/token/');
            return $res->json('access_token');
        });
    }
}
