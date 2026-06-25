<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'niu',
        'rccm',
        'tax_regime',
        'tax_center',
        'country_code',
        'plan_slug',
        'trial_ends_at',
        'subscription_started_at',
        'subscription_renewed_at',
        'next_billing_at',
        'custom_price_xaf',
        'vat_prorata_coefficient',
        'subscription_status',
        'onboarding_completed',
        'onboarding_step',
        'onboarding_completed_at',
        'onboarding_checklist_dismissed',
        'require_2fa',
        'phone',
        'email',
        'address',
        'logo_path',
        'letterhead_tagline',
        'letterhead_website',
        'bank_name',
        'bank_account',
        'bank_rib',
        'invoice_footer_note',
    ];

    protected $casts = [
        'vat_prorata_coefficient' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** Users who may access this company (many-to-many membership). */
    public function members()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    /** CEMAC country fiscal configuration. */
    public function countryConfig()
    {
        return $this->belongsTo(CountryConfig::class, 'country_code', 'country_code');
    }

    /** Standard VAT rate for this company's country (default Cameroon 19.25). */
    public function taxRate(): float
    {
        return (float) ($this->countryConfig?->vat_standard_rate ?? 19.25);
    }

    public function currencySymbol(): string
    {
        return $this->countryConfig?->currency_code ?? 'XAF';
    }

    /** Fiscal ID label (NIU / NIF) per country. */
    public function companyIdLabel(): string
    {
        return $this->countryConfig?->company_id_label ?? 'NIU';
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptionEvents()
    {
        return $this->hasMany(SubscriptionEvent::class);
    }

    public function hasValidFiscalProfile(): bool
    {
        return filled($this->niu) && filled($this->rccm) && filled($this->tax_center);
    }

    /** DGE or CIME-class centers require mandatory withholding fields on supplier invoices. */
    public function requiresWithholdingTax(): bool
    {
        return preg_match('/^(DGE|CIME)/i', $this->tax_center ?? '') === 1;
    }

    /** Prorata as a 0–1 multiplier for recoverable VAT computation. */
    public function prorataMultiplier(): string
    {
        return (string) BigDecimal::of((string) $this->vat_prorata_coefficient)
            ->dividedBy(BigDecimal::of('100'), 6, RoundingMode::HalfUp);
    }
}
