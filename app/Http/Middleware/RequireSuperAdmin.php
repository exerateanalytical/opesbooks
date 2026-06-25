<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'SUPER_ADMIN') {
            abort(403, 'Platform admin access required.');
        }
        return $next($request);
    }
}
