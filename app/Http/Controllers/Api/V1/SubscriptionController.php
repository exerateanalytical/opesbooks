<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    private static function planPrices(): array
    {
        return config('opes.plans', ['STARTER' => 5000, 'GROWTH' => 15000, 'ENTERPRISE' => 45000]);
    }

    /**
     * POST /api/v1/companies/{company}/subscriptions/initiate
     *
     * Initiates a subscription renewal via a local payment aggregator
     * (Maviance / Frikad / Bizao / CinetPay).
     * The aggregator pushes an STK prompt to the owner's phone.
     */
    public function initiate(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'plan'          => ['required', Rule::in(['STARTER', 'GROWTH', 'ENTERPRISE'])],
            'billing_phone' => 'required|string|regex:/^\+?[0-9]{9,15}$/',
        ]);

        $amountXaf = self::planPrices()[$data['plan']];

        // Create a PENDING subscription record
        $subscription = Subscription::create([
            'company_id'   => $company->id,
            'plan'         => $data['plan'],
            'amount_xaf'   => $amountXaf,
            'billing_phone' => $data['billing_phone'],
            'status'       => 'PENDING',
            'period_start' => now()->toDateString(),
            'period_end'   => now()->addMonth()->toDateString(),
        ]);

        // Build the aggregator payload (Maviance / Frikad compatible structure)
        $aggregatorPayload = $this->buildAggregatorPayload($subscription, $company);

        // In production: dispatch HTTP call to aggregator API
        // Here we return the payload and a mock reference to simulate the STK push
        $mockAggregatorRef = 'AGG-' . strtoupper(uniqid('OB', true));
        $subscription->update(['aggregator_reference' => $mockAggregatorRef]);

        return response()->json([
            'message'             => 'STK push initiated. Awaiting payment confirmation on phone.',
            'subscription_id'     => $subscription->id,
            'plan'                => $data['plan'],
            'amount_xaf'          => number_format($amountXaf, 0, '.', ',') . ' XAF',
            'billing_phone'       => $data['billing_phone'],
            'aggregator_ref'      => $mockAggregatorRef,
            'aggregator_payload'  => $aggregatorPayload,
            'period_end'          => $subscription->period_end->toDateString(),
        ], 202);
    }

    /**
     * POST /api/v1/companies/{company}/subscriptions/confirm
     *
     * Called by the payment aggregator webhook when the mobile money
     * STK push is confirmed by the subscriber.
     */
    public function confirm(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'aggregator_reference' => 'required|string',
            'payment_status'       => 'required|in:SUCCESS,FAILED,CANCELLED',
        ]);

        $subscription = Subscription::where('company_id', $company->id)
            ->where('aggregator_reference', $data['aggregator_reference'])
            ->firstOrFail();

        if ($data['payment_status'] === 'SUCCESS') {
            $subscription->update(['status' => 'ACTIVE']);
            $company->update(['subscription_status' => 'ACTIVE']);

            return response()->json([
                'message'      => 'Subscription activated successfully.',
                'subscription' => $subscription,
            ]);
        }

        $subscription->update(['status' => 'CANCELLED']);
        $company->update(['subscription_status' => 'PAST_DUE']);

        return response()->json([
            'message' => 'Payment failed or cancelled. Subscription not activated.',
            'status'  => $data['payment_status'],
        ], 422);
    }

    public function status(Company $company): JsonResponse
    {
        $active = Subscription::where('company_id', $company->id)
            ->where('status', 'ACTIVE')
            ->latest()
            ->first();

        return response()->json([
            'company_id'          => $company->id,
            'subscription_status' => $company->subscription_status,
            'active_plan'         => $active?->plan,
            'period_end'          => $active?->period_end?->toDateString(),
            'days_remaining'      => $active?->period_end?->diffInDays(now()),
        ]);
    }

    // GET /companies/{company}/subscriptions/receipt
    public function receipt(Company $company): \Illuminate\Http\Response
    {
        $subscription = Subscription::where('company_id', $company->id)
            ->where('status', 'ACTIVE')
            ->latest()
            ->firstOrFail();

        $receiptNumber = 'REC-' . $company->id . '-' . $subscription->id . '-' . date('Ymd');

        $pdf = Pdf::loadView('subscriptions.receipt', [
            'company'       => $company,
            'subscription'  => $subscription,
            'receiptNumber' => $receiptNumber,
        ])->setPaper([0, 0, 300, 500], 'portrait');

        return $pdf->download("recu-abonnement-{$receiptNumber}.pdf");
    }

    private function buildAggregatorPayload(Subscription $subscription, Company $company): array
    {
        return [
            'service_id'     => 'OPESBOOKS_SUBSCRIPTION',
            'merchant_name'  => 'Opes Books - Opesware',
            'subscriber_ref' => $company->niu,
            'amount'         => $subscription->amount_xaf,
            'currency'       => 'XAF',
            'description'    => "Abonnement Opes Books {$subscription->plan} — {$company->name}",
            'callback_url'   => url("/api/v1/companies/{$company->id}/subscriptions/confirm"),
            'phone'          => $subscription->billing_phone,
            'locale'         => 'fr',
        ];
    }
}
