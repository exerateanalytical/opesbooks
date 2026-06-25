<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'company_id', 'plan_slug', 'amount_xaf', 'currency', 'payment_method',
        'reference', 'period_start', 'period_end', 'status', 'receipt_number', 'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount_xaf'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::created(function (self $payment) {
            $company = $payment->company;
            app(\App\Services\WebhookService::class)->dispatch('payment.received', $payment->toArray(), $company);
            if ($company) {
                app(\App\Services\NotificationService::class)->pushOwners($company, [
                    'type'       => 'payment.received',
                    'title'      => 'Paiement reçu',
                    'body'       => number_format($payment->amount_xaf, 0, ',', ' ') . ' XAF · ' . $payment->receipt_number,
                    'icon'       => 'check-circle',
                    'icon_color' => 'text-emerald-400',
                    'action_url' => '/app?page=subscription',
                    'action_label' => 'Voir',
                ]);
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }

    public static function nextReceiptNumber(): string
    {
        $seq = static::whereYear('created_at', now()->year)->count() + 1;
        return 'REC-' . now()->year . '-' . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
