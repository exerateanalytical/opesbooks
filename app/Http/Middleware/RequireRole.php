<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces role-based access control for the three Opes Books user tiers.
 * Usage in routes: ->middleware('role:OWNER') or ->middleware('role:OWNER,ACCOUNTANT')
 */
class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Insufficient permissions. Required role: ' . implode(' or ', $roles),
                'your_role' => $user?->role ?? 'unauthenticated',
            ], 403);
        }

        return $next($request);
    }
}
