<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'webhook_endpoint_id', 'company_id', 'event_type', 'payload', 'status',
        'attempts', 'next_attempt_at', 'delivered_at', 'response_code',
        'response_body', 'error_message',
    ];

    protected $casts = [
        'payload'         => 'array',
        'next_attempt_at' => 'datetime',
        'delivered_at'    => 'datetime',
    ];

    public function endpoint() { return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id'); }
}
