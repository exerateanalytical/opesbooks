<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiConfig extends Model
{
    protected $fillable = [
        'company_id', 'gemini_api_key', 'ollama_enabled', 'ollama_model',
        'auto_categorize', 'auto_dsf_check', 'anomaly_scan',
    ];

    protected $casts = [
        'gemini_api_key'  => 'encrypted',
        'ollama_enabled'  => 'boolean',
        'auto_categorize' => 'boolean',
        'auto_dsf_check'  => 'boolean',
        'anomaly_scan'    => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
}
