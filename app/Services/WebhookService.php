<?php

namespace App\Services;

use App\Jobs\DeliverWebhookJob;
use App\Models\Company;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Str;

class WebhookService
{
    public function dispatch(string $event, array $payload, ?Company $company): void
    {
        if (! $company) {
            return;
        }

        $endpoints = WebhookEndpoint::where('company_id', $company->id)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($endpoints as $endpoint) {
            $delivery = WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'company_id'          => $company->id,
                'event_type'          => $event,
                'payload'             => [
                    'id'         => (string) Str::uuid(),
                    'event'      => $event,
                    'created_at' => now()->toIso8601String(),
                    'data'       => $payload,
                ],
                'status'              => 'pending',
                'next_attempt_at'     => now(),
            ]);

            DeliverWebhookJob::dispatch($delivery->id);
        }
    }

    public function sign(array $payload, string $secret): string
    {
        return 'sha256=' . hash_hmac('sha256', json_encode($payload), $secret);
    }
}
