<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Authenticates third-party integrators via an "ob_live_sk_…" / "ob_test_sk_…"
 * API key. Enforces per-key hourly rate limits, optional scope requirements,
 * and logs every request for observability.
 *
 * Usage: ->middleware('apikey')            (auth + rate limit + logging)
 *        ->middleware('apikey:invoices:read')  (also requires a scope)
 */
class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next, ?string $requiredScope = null)
    {
        $request->attributes->set('api_started_at', microtime(true));

        $bearer = $request->bearerToken();
        if (! $bearer || ! str_starts_with($bearer, 'ob_')) {
            $this->log($request, 401, null);
            abort(401, 'API key required.');
        }

        $key = ApiKey::where('key_hash', hash('sha256', $bearer))->first();
        if (! $key || ! $key->isUsable()) {
            $this->log($request, 401, $key);
            abort(401, 'Invalid or revoked API key.');
        }

        if ($requiredScope && ! $key->hasScope($requiredScope)) {
            $this->log($request, 403, $key);
            abort(403, "API key missing required scope: {$requiredScope}");
        }

        $limiterKey = 'apikey:' . $key->id;
        if (RateLimiter::tooManyAttempts($limiterKey, $key->rate_limit)) {
            $this->log($request, 429, $key);
            return response()->json([
                'message'     => 'Rate limit exceeded.',
                'retry_after' => RateLimiter::availableIn($limiterKey),
            ], 429);
        }
        RateLimiter::hit($limiterKey, 3600); // window = 1 hour

        $key->forceFill(['last_used_at' => now()])->saveQuietly();

        $request->attributes->set('api_key', $key);
        // Downstream controllers scope tenant data by this company id.
        app()->instance('current_api_company_id', $key->company_id);

        $response = $next($request);

        // Standard rate-limit headers for integrators.
        if (method_exists($response, 'header')) {
            $response->header('X-RateLimit-Limit', $key->rate_limit);
            $response->header('X-RateLimit-Remaining', max(0, RateLimiter::remaining($limiterKey, $key->rate_limit)));
            $response->header('X-RateLimit-Reset', now()->addSeconds(RateLimiter::availableIn($limiterKey) ?: 3600)->timestamp);
        }

        return $response;
    }

    /** Log a successful/authorized request after the response is sent. */
    public function terminate(Request $request, $response): void
    {
        $key = $request->attributes->get('api_key');
        if (! $key) {
            return; // rejected requests are logged inline in handle()
        }

        $this->log(
            $request,
            method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200,
            $key
        );
    }

    /** Persist a single API request log row. */
    private function log(Request $request, int $status, ?ApiKey $key): void
    {
        $start = $request->attributes->get('api_started_at');
        ApiRequestLog::create([
            'api_key_id'  => $key?->id,
            'company_id'  => $key?->company_id,
            'method'      => $request->method(),
            'endpoint'    => '/' . ltrim($request->path(), '/'),
            'status_code' => $status,
            'latency_ms'  => $start ? (int) round((microtime(true) - $start) * 1000) : 0,
            'ip'          => $request->ip(),
            'created_at'  => now(),
        ]);
    }
}
