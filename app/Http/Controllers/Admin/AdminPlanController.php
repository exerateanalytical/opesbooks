<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanConfig;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = PlanConfig::orderBy('sort_order')->get();
        return view('admin.plans', compact('plans'));
    }

    public function update(Request $request, PlanConfig $plan)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:60',
            'price_xaf_monthly' => 'required|integer|min:0',
            'price_xaf_yearly'  => 'required|integer|min:0',
            'max_users'         => 'required|integer|min:-1',
        ]);

        $plan->update(array_merge($data, ['is_active' => $request->boolean('is_active')]));

        return back()->with('success', "Plan {$plan->name} mis à jour.");
    }
}
