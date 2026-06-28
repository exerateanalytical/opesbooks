<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies'    => Company::count(),
            'total_users'        => User::whereNotIn('role', ['SUPER_ADMIN'])->count(),
            'active_subs'        => Subscription::where('status', 'ACTIVE')->count(),
            'revenue_this_month' => Subscription::where('status', 'ACTIVE')
                ->whereMonth('created_at', now()->month)
                ->sum('amount_xaf'),
        ];
        $companies = Company::with(['users', 'subscriptions' => fn($q) => $q->latest()])->latest()->paginate(20);
        return view('admin.dashboard', compact('stats', 'companies'));
    }

    public function users(Request $request)
    {
        $users = User::with('company')
            ->whereNotIn('role', ['SUPER_ADMIN'])
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) =>
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function company(Company $company)
    {
        $company->load(['users', 'subscriptions']);

        // Operational health snapshot for dunning / engagement assessment.
        $health = [
            'journal_entries' => $company->journalEntries()->count(),
            'last_entry'      => $company->journalEntries()->max('created_at'),
            'users'           => $company->users()->count(),
            'last_login'      => $company->users()->max('last_login_at'),
            'payments_total'  => (float) $company->payments()->where('status', 'completed')->sum('amount_xaf'),
        ];

        return view('admin.company', compact('company', 'health'));
    }

    /** POST /admin/companies/{company}/suspend — hard suspend (blocks tenant API access). */
    public function suspendCompany(Request $request, Company $company)
    {
        $company->update(['subscription_status' => 'SUSPENDED']);
        return back()->with('success', "Entreprise suspendue : {$company->name}");
    }

    /** POST /admin/companies/{company}/reactivate */
    public function reactivateCompany(Request $request, Company $company)
    {
        $company->update(['subscription_status' => 'ACTIVE']);
        return back()->with('success', "Entreprise réactivée : {$company->name}");
    }

    /** DELETE /admin/companies/{company} — soft delete + revoke member tokens. */
    public function destroyCompany(Request $request, Company $company)
    {
        foreach ($company->users as $u) {
            $u->tokens()->delete();
        }
        $company->delete();
        return redirect()->route('admin.companies')->with('success', "Entreprise supprimée : {$company->name}");
    }

    public function updateSubscription(Request $request, Company $company)
    {
        $data = $request->validate([
            'plan'       => 'required|in:STARTER,GROWTH,ENTERPRISE',
            'status'     => 'required|in:ACTIVE,SUSPENDED,CANCELLED',
            'expires_at' => 'required|date',
        ]);

        // The form field is "expires_at"; the column is "period_end".
        $attributes = [
            'plan'       => $data['plan'],
            'status'     => $data['status'],
            'period_end' => $data['expires_at'],
        ];

        $sub = $company->subscriptions()->latest()->first();
        if ($sub) {
            $sub->update($attributes);
        } else {
            $company->subscriptions()->create(array_merge($attributes, [
                'amount_xaf'   => 0,
                'period_start' => now(),
            ]));
        }

        return back()->with('success', 'Subscription updated.');
    }

    public function impersonate(Request $request, User $user)
    {
        // Authorization lives in the controller, not just the Blade template:
        // an admin may never assume another platform admin's identity, nor their own.
        if ($user->isSuperAdmin()) {
            abort(403, 'Cannot impersonate another platform admin.');
        }
        if ($user->id === $request->user()->id) {
            abort(403, 'Cannot impersonate yourself.');
        }

        $adminId = $request->user()->id;

        // The token carries an impersonated_by:<adminId> ability so tenant-side
        // requests are attributable to the acting admin, and is short-lived.
        $newToken = $user->createToken(
            'admin-impersonate',
            ['*', 'impersonated_by:' . $adminId],
            now()->addHour()
        );

        // Audit trail: record who impersonated whom.
        \App\Models\AuditLog::create([
            'user_id'    => $adminId,
            'company_id' => $user->company_id,
            'action'     => 'IMPERSONATE',
            'model_type' => User::class,
            'model_id'   => $user->id,
            'new_values' => ['impersonated_email' => $user->email],
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        // Dedicated impersonation log (start) — now records the target and the
        // token id so the session can be revoked precisely on exit.
        \App\Models\AdminImpersonationLog::create([
            'admin_user_id'  => $adminId,
            'target_user_id' => $user->id,
            'target_email'   => $user->email,
            'token_id'       => $newToken->accessToken->id,
            'company_id'     => $user->company_id,
            'started_at'     => now(),
            'ip_address'     => $request->ip(),
            'created_at'     => now(),
        ]);

        $company = $user->company;

        return response()->json([
            'token'        => $newToken->plainTextToken,
            'user'         => $user->only('id', 'name', 'email', 'role'),
            'company_name' => $company?->name,
        ]);
    }

    /** GET /admin/impersonate/leave — end the active impersonation, revoke its token, return to admin. */
    public function leaveImpersonation(Request $request)
    {
        $log = \App\Models\AdminImpersonationLog::where('admin_user_id', $request->user()->id)
            ->whereNull('ended_at')
            ->latest('id')
            ->first();

        if ($log) {
            $log->update(['ended_at' => now()]);
            // Revoke the impersonation token so a copied bearer can't outlive the exit.
            if ($log->token_id) {
                \Laravel\Sanctum\PersonalAccessToken::where('id', $log->token_id)->delete();
            }
        }

        return redirect()->route('admin.companies')->with('success', 'Impersonnification terminée.');
    }
}
