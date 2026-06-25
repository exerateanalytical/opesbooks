<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MecefConfig extends Model
{
    protected $fillable = [
        'company_id', 'nim', 'api_endpoint', 'api_token', 'is_active', 'sandbox_mode',
    ];

    protected $casts = [
        'api_token'    => 'encrypted',
        'is_active'    => 'boolean',
        'sandbox_mode' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
}
