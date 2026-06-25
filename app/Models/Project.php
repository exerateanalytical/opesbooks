<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'company_id', 'name', 'code', 'client_id', 'description', 'status',
        'start_date', 'end_date', 'budget_amount', 'contract_value',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'budget_amount'  => 'float',
        'contract_value' => 'float',
    ];

    public const STATUSES = ['draft', 'active', 'completed', 'cancelled'];

    public function company() { return $this->belongsTo(Company::class); }
    public function client()  { return $this->belongsTo(Customer::class, 'client_id'); }
    public function entries() { return $this->hasMany(ProjectEntry::class)->orderByDesc('entry_date'); }

    public function totalRevenue(): float
    {
        return (float) $this->entries()->where('amount', '>', 0)->sum('amount');
    }

    public function totalCosts(): float
    {
        return (float) abs($this->entries()->where('amount', '<', 0)->sum('amount'));
    }

    public function profit(): float
    {
        return $this->totalRevenue() - $this->totalCosts();
    }
}
