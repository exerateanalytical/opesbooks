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
        'vat_prorata_coefficient',
        'subscription_status',
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

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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
