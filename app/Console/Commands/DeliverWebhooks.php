<?php

namespace App\Console\Commands;

use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\WebhookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DeliverWebhooks extends Command
{
    protected $signature = 'webhooks:deliver';
    protected $description = 'Deliver pending/retrying webhook events with exponential backoff';

    /** Backoff per attempt index: 1min, 5min, 30min, 2h, 24h. */
    private array $backoff = [60, 300, 1800, 7200, 86400];

    public function handle(WebhookService $svc): int
    {
        $due = WebhookDelivery::whereIn('status', ['pending', 'retrying'])
            ->where('next_attempt_at', '<=', now())
            ->with('endpoint')
            ->limit(100)
            ->get();

        foreach ($due as $delivery) {
            $endpoint = $delivery->endpoint;
            if (! $endpoint || ! $endpoint->is_active) {
                $delivery->update(['status' => 'failed', 'error_message' => 'Endpoint inactive']);
                continue;
            }

            $signature = $svc->sign($delivery->payload, $endpoint->secret);

            try {
                $res = Http::timeout(10)->withHeaders([
                    'Content-Type'          => 'application/json',
                    'X-OPESBooks-Signature' => $signature,
                    'X-OPESBooks-Event'     => $delivery->event_type,
                    'X-OPESBooks-Delivery'  => (string) $delivery->id,
                ])->post($endpoint->url, $delivery->payload);

                if ($res->successful()) {
                    $delivery->update([
                        'status'        => 'delivered',
                        'attempts'      => $delivery->attempts + 1,
                        'delivered_at'  => now(),
                        'response_code' => $res->status(),
                        'response_body' => Str::limit($res->body(), 500),
                    ]);
                    $endpoint->update(['failure_count' => 0, 'last_triggered_at' => now()]);
                } else {
                    $this->markFailed($delivery, $endpoint, $res->status(), $res->body());
                }
            } catch (\Throwable $e) {
                $this->markFailed($delivery, $endpoint, 0, $e->getMessage());
            }
        }

        $this->info("Processed {$due->count()} webhook deliveries.");
        return self::SUCCESS;
    }

    private function markFailed(WebhookDelivery $delivery, WebhookEndpoint $endpoint, int $code, string $body): void
    {
        $next = $this->backoff[$delivery->attempts] ?? null;
        $delivery->update([
            'status'          => $next ? 'retrying' : 'failed',
            'attempts'        => $delivery->attempts + 1,
            'next_attempt_at' => $next ? now()->addSeconds($next) : null,
            'response_code'   => $code ?: null,
            'error_message'   => Str::limit($body, 500),
        ]);
        $endpoint->increment('failure_count');
        if ($endpoint->failure_count >= 50) {
            $endpoint->update(['is_active' => false]);
        }
    }
}
