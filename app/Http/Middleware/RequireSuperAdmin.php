<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireSuperAdmin
{
    /** Admin console idle timeout, in seconds. */
    private const IDLE_LIMIT = 30 * 60;

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'SUPER_ADMIN') {
            abort(403, 'Platform admin access required.');
        }

        // Idle timeout: the privileged console must not stay open as long as an
        // ordinary 120-minute tenant session.
        $last = $request->session()->get('admin_last_activity');
        if ($last !== null && (now()->timestamp - (int) $last) > self::IDLE_LIMIT) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Session expirée par inactivité. Reconnectez-vous.']);
        }
        $request->session()->put('admin_last_activity', now()->timestamp);

        return $next($request);
    }
}
