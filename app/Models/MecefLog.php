<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MecefLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'invoice_id', 'action', 'request_payload',
        'response_payload', 'http_status', 'created_at',
    ];

    protected $casts = [
        'request_payload'  => 'array',
        'response_payload' => 'array',
        'created_at'       => 'datetime',
    ];
}
