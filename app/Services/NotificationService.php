<?php

namespace App\Services;

use App\Models\Company;
use App\Models\InAppNotification;
use App\Models\User;

class NotificationService
{
    /** Push an in-app notification to every user of a company (or one user). */
    public function push(Company $company, array $data, ?User $user = null): void
    {
        $targets = $user ? collect([$user]) : $company->users()->get();

        foreach ($targets as $u) {
            InAppNotification::create([
                'company_id'   => $company->id,
                'user_id'      => $u->id,
                'type'         => $data['type'] ?? null,
                'title'        => $data['title'],
                'body'         => $data['body'] ?? null,
                'icon'         => $data['icon'] ?? 'bell',
                'icon_color'   => $data['icon_color'] ?? 'text-amber-400',
                'action_url'   => $data['action_url'] ?? null,
                'action_label' => $data['action_label'] ?? null,
            ]);
        }
    }

    /** Push to the OWNER(s) only. */
    public function pushOwners(Company $company, array $data): void
    {
        foreach ($company->users()->where('role', 'OWNER')->get() as $owner) {
            $this->push($company, $data, $owner);
        }
    }
}
