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

        $sub = $company->subscriptions()->latest()->first();
        if ($sub) {
            $sub->update($data);
        } else {
            $company->subscriptions()->create(array_merge($data, ['amount_xaf' => 0]));
        }

        return back()->with('success', 'Subscription updated.');
    }

    public function impersonate(User $user)
    {
        $token = $user->createToken('admin-impersonate', ['*'], now()->addHour())->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user->only('id', 'name', 'email', 'role')]);
    }
}
