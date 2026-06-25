<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Orange Money Cameroon (Web Payment). Runs in sandbox mode until real
 * merchant credentials are configured — sandbox returns an immediately
 * "successful" payment so the end-to-end flow is testable.
 */
class OrangeMoneyService
{
    public function isSandbox(): bool
    {
        return config('payment.environment') !== 'live'
            || empty(config('payment.orange_money.merchant_key'));
    }

    public function initPayment(array $data): array
    {
        if ($this->isSandbox()) {
            return [
                'success'     => true,
                'sandbox'     => true,
                'status'      => 'SUCCESSFUL',
                'pay_token'   => 'OM-SBX-' . Str::upper(Str::random(16)),
                'payment_url' => null,
            ];
        }

        try {
            $res = Http::asJson()->post(rtrim(config('payment.orange_money.base_url'), '/') . '/webpayment', [
                'merchant_key' => config('payment.orange_money.merchant_key'),
                'currency'     => 'XAF',
                'order_id'     => $data['order_id'],
                'amount'       => $data['amount'],
                'lang'         => 'fr',
                'reference'    => 'OPB-' . $data['order_id'],
            ]);
            $body = $res->json();
            return [
                'success'     => $res->successful(),
                'status'      => $body['status'] ?? 'PENDING',
                'pay_token'   => $body['pay_token'] ?? null,
                'payment_url' => $body['payment_url'] ?? null,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }

    public function verifyPayment(string $payToken): array
    {
        if ($this->isSandbox()) {
            return ['status' => 'SUCCESSFUL', 'sandbox' => true];
        }
        try {
            $res = Http::get(rtrim(config('payment.orange_money.base_url'), '/') . '/transactionstatus', [
                'merchant_key' => config('payment.orange_money.merchant_key'),
                'pay_token'    => $payToken,
            ]);
            return $res->json() ?: ['status' => 'PENDING'];
        } catch (\Throwable $e) {
            return ['status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }
}
