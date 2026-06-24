<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks API access for companies with SUSPENDED subscription status.
 * PAST_DUE companies get a grace warning header but are still allowed through.
 */
class RequireActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user    = $request->user();
        $company = $user?->company;

        if (! $company) {
            return $next($request);
        }

        if ($company->subscription_status === 'SUSPENDED') {
            return response()->json([
                'message' => 'Account suspended. Please renew your Opes Books subscription via mobile money to restore access.',
                'subscription_status' => 'SUSPENDED',
            ], 402);
        }

        $response = $next($request);

        if ($company->subscription_status === 'PAST_DUE') {
            $response->headers->set('X-Subscription-Warning', 'PAST_DUE: payment overdue, service will suspend soon');
        }

        return $response;
    }
}
