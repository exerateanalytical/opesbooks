<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 5;
    public int $timeout = 30;

    public function __construct(private int $deliveryId) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::with('endpoint')->find($this->deliveryId);

        if (! $delivery || $delivery->status === 'delivered') {
            return;
        }

        $endpoint = $delivery->endpoint;
        if (! $endpoint || ! $endpoint->is_active) {
            $delivery->update(['status' => 'skipped']);
            return;
        }

        $payload   = $delivery->payload;
        $signature = 'sha256=' . hash_hmac('sha256', json_encode($payload), $endpoint->secret ?? '');

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type'        => 'application/json',
                    'X-OPESBooks-Event'   => $delivery->event_type,
                    'X-OPESBooks-Signature' => $signature,
                    'X-OPESBooks-Delivery'  => $delivery->id,
                    'User-Agent'          => 'OPESBooks-Webhooks/1.0',
                ])
                ->post($endpoint->url, $payload);

            $success = $response->successful();

            $delivery->update([
                'status'          => $success ? 'delivered' : 'failed',
                'response_status' => $response->status(),
                'response_body'   => substr($response->body(), 0, 500),
                'attempted_at'    => now(),
                'attempts'        => ($delivery->attempts ?? 0) + 1,
            ]);

            if (! $success) {
                $this->scheduleRetry($delivery);
            }
        } catch (\Throwable $e) {
            $delivery->update([
                'status'       => 'failed',
                'response_body'=> $e->getMessage(),
                'attempted_at' => now(),
                'attempts'     => ($delivery->attempts ?? 0) + 1,
            ]);
            Log::warning('Webhook delivery failed', ['delivery_id' => $this->deliveryId, 'error' => $e->getMessage()]);
            $this->scheduleRetry($delivery);
        }
    }

    private function scheduleRetry(WebhookDelivery $delivery): void
    {
        $attempts = $delivery->attempts ?? 1;
        if ($attempts >= 5) {
            $delivery->update(['status' => 'abandoned']);
            return;
        }
        // Exponential backoff: 1min, 5min, 30min, 2h, 8h
        $delays = [60, 300, 1800, 7200, 28800];
        $delay  = $delays[$attempts - 1] ?? 28800;

        $delivery->update(['next_attempt_at' => now()->addSeconds($delay)]);
        static::dispatch($delivery->id)->delay(now()->addSeconds($delay));
    }
}
