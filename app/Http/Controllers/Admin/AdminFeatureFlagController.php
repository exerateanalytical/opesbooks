<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FeatureFlag;
use App\Services\FeatureFlagService;
use Illuminate\Http\Request;

class AdminFeatureFlagController extends Controller
{
    public const TARGETS = [
        'all'                  => 'Tous',
        'none'                 => 'Aucun',
        'plan_starter_plus'    => 'Starter +',
        'plan_business_plus'   => 'Business +',
        'plan_enterprise_only' => 'Enterprise',
        'specific_companies'   => 'Personnalisé',
    ];

    public function index()
    {
        $flags     = FeatureFlag::orderBy('name')->get();
        $targets   = self::TARGETS;
        $companies = Company::orderBy('name')->get(['id', 'name']);
        return view('admin.feature-flags', compact('flags', 'targets', 'companies'));
    }

    public function update(Request $request, FeatureFlag $flag)
    {
        $data = $request->validate([
            'enabled_for'            => 'required|in:' . implode(',', array_keys(self::TARGETS)),
            'specific_company_ids'   => 'nullable|array',
            'specific_company_ids.*' => 'integer',
        ]);

        $update = ['enabled_for' => $data['enabled_for']];
        // Only touch the company list for the 'specific_companies' target, so
        // switching to another target doesn't silently wipe a saved list.
        if ($data['enabled_for'] === 'specific_companies') {
            $update['specific_company_ids'] = $data['specific_company_ids'] ?? [];
        }
        $flag->update($update);
        app(FeatureFlagService::class)->clearCache($flag->key);

        return back()->with('success', "Fonctionnalité « {$flag->name} » mise à jour.");
    }
}
