<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $flags   = FeatureFlag::orderBy('name')->get();
        $targets = self::TARGETS;
        return view('admin.feature-flags', compact('flags', 'targets'));
    }

    public function update(Request $request, FeatureFlag $flag)
    {
        $data = $request->validate([
            'enabled_for'            => 'required|in:' . implode(',', array_keys(self::TARGETS)),
            'specific_company_ids'   => 'nullable|array',
            'specific_company_ids.*' => 'integer',
        ]);
        $flag->update([
            'enabled_for'          => $data['enabled_for'],
            'specific_company_ids' => $data['specific_company_ids'] ?? null,
        ]);
        app(FeatureFlagService::class)->clearCache($flag->key);

        return back()->with('success', "Fonctionnalité « {$flag->name} » mise à jour.");
    }
}
