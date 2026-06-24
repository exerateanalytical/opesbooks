<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'company_id',
        'plan',
        'amount_xaf',
        'billing_phone',
        'status',
        'aggregator_reference',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount_xaf'   => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE' && $this->period_end->isFuture();
    }
}
