<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tenant isolation for /companies/{company}/* routes.
 *
 * The {company} route param is bound globally (any id resolves), so without this
 * check any authenticated user could read/write another tenant's data by passing
 * a foreign company id. We allow access when the company is the user's active
 * company OR one they are a member of (multi-company / firm context).
 */
class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user    = $request->user();
        $company = $request->route('company');
        $companyId = is_object($company) ? (int) $company->id : (int) $company;

        if (! $user || ! $companyId) {
            abort(403, 'Company access denied.');
        }

        if ((int) $user->company_id === $companyId || $user->belongsToCompany($companyId)) {
            return $next($request);
        }

        abort(403, 'You do not have access to this company.');
    }
}
