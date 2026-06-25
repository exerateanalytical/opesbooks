<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

class FeatureFlagService
{
    /** Is a feature enabled for the current (or given) company? */
    public function enabled(string $key, ?Company $company = null): bool
    {
        $company ??= auth()->user()?->company;

        $flag = Cache::remember("feature_flag:{$key}", 300, fn () => FeatureFlag::where('key', $key)->first());
        if (! $flag) {
            return false; // unknown flag = off (fail-safe)
        }

        $plan = $company?->plan_slug ?? 'free';
        $rank = FeatureFlag::PLAN_RANK[$plan] ?? 0;

        return match ($flag->enabled_for) {
            'all'                 => true,
            'none'                => false,
            'plan_starter_plus'   => $rank >= FeatureFlag::PLAN_RANK['starter'],
            'plan_business_plus'  => $rank >= FeatureFlag::PLAN_RANK['business'],
            'plan_enterprise_only'=> $rank >= FeatureFlag::PLAN_RANK['enterprise'],
            'specific_companies'  => $company && in_array($company->id, $flag->specific_company_ids ?? [], true),
            default               => false,
        };
    }

    public function enabledFor(string $key, Company $company): bool
    {
        return $this->enabled($key, $company);
    }

    public function clearCache(string $key): void
    {
        Cache::forget("feature_flag:{$key}");
    }
}
