<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $fillable = [
        'company_id', 'api_key_id', 'url', 'events', 'secret',
        'is_active', 'last_triggered_at', 'failure_count',
    ];

    protected $casts = [
        'events'            => 'array',
        'is_active'         => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    protected $hidden = ['secret'];

    public const EVENTS = [
        'invoice.created', 'invoice.updated', 'invoice.paid', 'invoice.overdue',
        'invoice.voided', 'invoice.certified_mecef', 'journal.entry.posted',
        'client.created', 'client.updated', 'supplier.created', 'payment.received',
        'subscription.upgraded', 'subscription.cancelled', 'tax.dsf.ready',
        'stock.low_alert', 'payroll.processed', 'project.completed', 'crm.lead.won',
    ];

    public function company()   { return $this->belongsTo(Company::class); }
    public function deliveries(){ return $this->hasMany(WebhookDelivery::class); }
}
