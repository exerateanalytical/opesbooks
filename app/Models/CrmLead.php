<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model
{
    protected $fillable = [
        'company_id', 'client_id', 'contact_name', 'contact_phone', 'contact_email',
        'company_name', 'source', 'estimated_value', 'status', 'notes',
        'assigned_to', 'lost_reason', 'stage_changed_at',
    ];

    protected $casts = [
        'estimated_value'  => 'float',
        'stage_changed_at' => 'datetime',
    ];

    public const STATUSES = ['new', 'contacted', 'qualified', 'proposal_sent', 'won', 'lost'];

    public function company()  { return $this->belongsTo(Company::class); }
    public function client()   { return $this->belongsTo(Customer::class, 'client_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function activities() { return $this->hasMany(CrmActivity::class, 'lead_id')->latest(); }
}
