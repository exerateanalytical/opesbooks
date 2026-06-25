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

    public function users()
    {
        $users = User::with('company')->whereNotIn('role', ['SUPER_ADMIN'])->latest()->paginate(30);
        return view('admin.users', compact('users'));
    }

    public function company(Company $company)
    {
        $company->load(['users', 'subscriptions']);
        return view('admin.company', compact('company'));
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
        $token = $user->createToken('admin-impersonate', ['*'], now()->addHour())->plainTextToken;

        // Audit trail: record who impersonated whom.
        \App\Models\AuditLog::create([
            'user_id'    => $request->user()->id,
            'company_id' => $user->company_id,
            'action'     => 'IMPERSONATE',
            'model_type' => User::class,
            'model_id'   => $user->id,
            'new_values' => ['impersonated_email' => $user->email],
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return response()->json(['token' => $token, 'user' => $user->only('id', 'name', 'email', 'role')]);
    }
}
