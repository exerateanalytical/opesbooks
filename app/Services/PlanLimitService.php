<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\PlanConfig;

class PlanLimitService
{
    private function plan(Company $company): PlanConfig
    {
        return PlanConfig::where('slug', $company->plan_slug ?? 'free')->first()
            ?? PlanConfig::where('slug', 'free')->firstOrNew([], [
                'max_invoices_per_month' => 20, 'max_users' => 1, 'api_calls_per_hour' => 0,
            ]);
    }

    public function canCreateInvoice(Company $company): bool
    {
        $max = $this->plan($company)->max_invoices_per_month;
        if ($max === -1) {
            return true;
        }
        $count = CustomerInvoice::where('company_id', $company->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        return $count < $max;
    }

    public function canAddUser(Company $company): bool
    {
        $max = $this->plan($company)->max_users;
        return $max === -1 || $company->users()->count() < $max;
    }

    public function canUseApi(Company $company): bool
    {
        return ($this->plan($company)->api_calls_per_hour ?? 0) !== 0;
    }

    /** Structured limit info for an over-limit API response. */
    public function limitReached(Company $company, string $resource): array
    {
        $plan = $this->plan($company);
        return [
            'error'        => 'plan_limit_reached',
            'message'      => "Limite du plan {$plan->name} atteinte ({$resource}). Passez à un plan supérieur.",
            'plan'         => $plan->slug,
            'resource'     => $resource,
        ];
    }
}
