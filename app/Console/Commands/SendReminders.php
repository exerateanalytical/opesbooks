<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CustomerInvoice;
use App\Models\InAppNotification;
use App\Models\User;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'notifications:reminders';
    protected $description = 'Create reminder notifications (overdue invoices, DSF, subscription renewals)';

    public function handle(): int
    {
        $sent = 0;
        foreach (Company::query()->get() as $company) {
            $owners = User::where('company_id', $company->id)->where('role', 'OWNER')->get();
            if ($owners->isEmpty()) {
                continue;
            }

            // Overdue invoices (summary, once/day).
            $overdue = CustomerInvoice::where('company_id', $company->id)
                ->whereIn('status', ['SENT', 'OVERDUE'])
                ->whereDate('due_date', '<', now())->count();
            if ($overdue > 0) {
                $sent += $this->notify($owners, $company, 'invoice.overdue',
                    "{$overdue} facture(s) en retard",
                    "Vous avez {$overdue} facture(s) impayée(s) au-delà de l'échéance.",
                    'AlertTriangle', 'text-red-400', '/app?page=customer-invoices');
            }

            // DSF reminder on the 15th.
            if (now()->day === 15) {
                $sent += $this->notify($owners, $company, 'tax.dsf',
                    'Rappel : Déclaration DSF',
                    'Pensez à préparer votre déclaration fiscale (DSF) ce mois-ci.',
                    'FileSpreadsheet', 'text-amber-400', '/app?page=dsf-export');
            }

            // Subscription renewal within 7 days.
            if ($company->next_billing_at && $company->next_billing_at->between(now(), now()->addDays(7))) {
                $sent += $this->notify($owners, $company, 'subscription.renewal',
                    'Renouvellement d\'abonnement',
                    'Votre abonnement se renouvelle le ' . $company->next_billing_at->format('d/m/Y') . '.',
                    'CreditCard', 'text-blue-400', '/app?page=subscription');
            }
        }

        $this->info("Created {$sent} reminder notifications.");
        return self::SUCCESS;
    }

    /** Create a notification per owner, skipping duplicates already created today. */
    private function notify($owners, Company $company, string $type, string $title, string $body, string $icon, string $color, string $url): int
    {
        $n = 0;
        foreach ($owners as $owner) {
            $exists = InAppNotification::where('user_id', $owner->id)
                ->where('type', $type)
                ->whereDate('created_at', today())->exists();
            if ($exists) {
                continue;
            }
            InAppNotification::create([
                'company_id' => $company->id, 'user_id' => $owner->id, 'type' => $type,
                'title' => $title, 'body' => $body, 'icon' => $icon, 'icon_color' => $color,
                'action_url' => $url, 'action_label' => 'Voir',
            ]);
            $n++;
        }
        return $n;
    }
}
