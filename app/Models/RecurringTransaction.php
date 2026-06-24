<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'company_id', 'name', 'debit_account', 'credit_account', 'amount_xaf',
        'memo', 'frequency', 'start_date', 'end_date', 'next_run_date',
        'last_run_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date',
        'next_run_date' => 'date', 'last_run_date' => 'date',
        'is_active' => 'boolean', 'amount_xaf' => 'float',
    ];

    public function company() { return $this->belongsTo(Company::class); }

    public function advanceNextRun(): void
    {
        $next = match($this->frequency) {
            'DAILY'     => $this->next_run_date->addDay(),
            'WEEKLY'    => $this->next_run_date->addWeek(),
            'MONTHLY'   => $this->next_run_date->addMonth(),
            'QUARTERLY' => $this->next_run_date->addMonths(3),
            'YEARLY'    => $this->next_run_date->addYear(),
        };
        $this->update([
            'last_run_date' => $this->next_run_date,
            'next_run_date' => $next,
            'is_active'     => $this->end_date ? $next->lte($this->end_date) : true,
        ]);
    }
}
