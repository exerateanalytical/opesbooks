<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read-only account guard.
 *
 * Users with the AUDITOR role (commissaire aux comptes / external reviewer) may
 * read, export and print everything, but cannot create, edit or delete. Any
 * mutating HTTP method is rejected with 403.
 */
class EnforceReadOnlyRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'AUDITOR'
            && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return response()->json([
                'message' => 'Compte en lecture seule (Auditeur) : action non autorisée.',
                'read_only' => true,
            ], 403);
        }

        return $next($request);
    }
}
