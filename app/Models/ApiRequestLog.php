<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_key_id', 'company_id', 'method', 'endpoint',
        'status_code', 'latency_ms', 'ip', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
