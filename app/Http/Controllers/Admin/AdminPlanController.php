<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PlanConfig;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = PlanConfig::orderBy('sort_order')->get();
        // Live tenant count per plan slug, to warn before hiding an in-use plan.
        $usage = Company::selectRaw('plan_slug, COUNT(*) as n')->groupBy('plan_slug')->pluck('n', 'plan_slug');
        return view('admin.plans', compact('plans', 'usage'));
    }

    public function update(Request $request, PlanConfig $plan)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:60',
            'price_xaf_monthly'      => 'required|integer|min:0',
            'price_xaf_yearly'       => 'required|integer|min:0',
            'max_users'              => 'required|integer|min:-1',
            'max_invoices_per_month' => 'required|integer|min:-1',
            'api_calls_per_hour'     => 'required|integer|min:-1',
        ]);

        $isActive = $request->boolean('is_active');
        $plan->update(array_merge($data, ['is_active' => $isActive]));

        // Warn (don't block) if a still-subscribed plan was just hidden.
        if (! $isActive) {
            $onPlan = Company::where('plan_slug', $plan->slug)->count();
            if ($onPlan > 0) {
                return back()->with('warning', "Plan {$plan->name} désactivé — {$onPlan} entreprise(s) y sont encore abonnées.");
            }
        }

        return back()->with('success', "Plan {$plan->name} mis à jour.");
    }
}
