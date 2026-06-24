<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditActivity
{
    private const AUDITED_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!in_array($request->method(), self::AUDITED_METHODS)) {
            return $response;
        }

        $user = $request->user();
        if (!$user) {
            return $response;
        }

        $companyId = $request->route('company')?->id ?? null;
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            try {
                AuditLog::create([
                    'user_id'    => $user->id,
                    'company_id' => $companyId,
                    'action'     => $request->method() . ':' . $request->path(),
                    'model_type' => null,
                    'model_id'   => null,
                    'old_values' => null,
                    'new_values' => $request->except(['password', 'password_confirmation']),
                    'ip_address' => $request->ip(),
                ]);
            } catch (\Throwable) {
                // Never block a request due to audit failure
            }
        }

        return $response;
    }
}
