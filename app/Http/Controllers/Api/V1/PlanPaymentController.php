<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PlanConfig;
use App\Models\SubscriptionEvent;
use App\Services\MtnMomoService;
use App\Services\OrangeMoneyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanPaymentController extends Controller
{
    /** GET /api/v1/plans — public plan catalogue. */
    public function plans(): JsonResponse
    {
        return response()->json(PlanConfig::where('is_active', true)->orderBy('sort_order')->get());
    }

    /** POST /api/v1/companies/plan/pay — self-service upgrade via OM / MTN. */
    public function pay(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_slug'      => ['required', 'exists:plan_configs,slug'],
            'payment_method' => ['required', Rule::in(['orange_money', 'mtn_momo', 'bank_transfer'])],
            'phone'          => 'required_if:payment_method,mtn_momo|nullable|string|max:20',
        ]);

        $company = $request->user()->company;
        $plan    = PlanConfig::where('slug', $data['plan_slug'])->firstOrFail();
        $orderId = 'ORD-' . Str::upper(Str::random(8));
        $amount  = $company->custom_price_xaf ?: $plan->price_xaf_monthly;

        $payment = Payment::create([
            'company_id'     => $company->id,
            'plan_slug'      => $plan->slug,
            'amount_xaf'     => $amount,
            'currency'       => 'XAF',
            'payment_method' => $data['payment_method'],
            'reference'      => $orderId,
            'status'         => 'pending',
            'period_start'   => now(),
            'period_end'     => now()->addMonth(),
        ]);

        $result = match ($data['payment_method']) {
            'orange_money' => app(OrangeMoneyService::class)->initPayment(['order_id' => $orderId, 'amount' => $amount]),
            'mtn_momo'     => app(MtnMomoService::class)->requestPayment(['order_id' => $orderId, 'amount' => $amount, 'phone' => $data['phone'], 'plan_name' => $plan->name]),
            default        => ['success' => true, 'status' => 'PENDING'], // bank transfer = manual confirm
        };

        // Sandbox / immediate success → activate the plan now.
        if (($result['status'] ?? '') === 'SUCCESSFUL') {
            $this->activate($payment, $company, $plan, $amount);
        }

        return response()->json([
            'ok'        => $result['success'] ?? false,
            'status'    => $result['status'] ?? 'PENDING',
            'sandbox'   => $result['sandbox'] ?? false,
            'payment'   => $payment->fresh(),
            'reference' => $orderId,
        ], 201);
    }

    private function activate(Payment $payment, $company, PlanConfig $plan, int $amount): void
    {
        $payment->update(['status' => 'completed', 'receipt_number' => Payment::nextReceiptNumber()]);

        $company->update([
            'plan_slug'               => $plan->slug,
            'subscription_status'     => 'ACTIVE',
            'subscription_started_at' => $company->subscription_started_at ?? now(),
            'subscription_renewed_at' => now(),
            'next_billing_at'         => now()->addMonth(),
        ]);

        SubscriptionEvent::create([
            'company_id' => $company->id,
            'event_type' => 'payment_received',
            'to_plan'    => $plan->slug,
            'amount_xaf' => $amount,
            'created_at' => now(),
        ]);
    }
}
