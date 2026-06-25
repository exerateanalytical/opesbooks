<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'user_id', 'feature', 'input_data', 'suggestion',
        'model_used', 'was_online', 'was_accepted', 'response_time_ms', 'created_at',
    ];

    protected $casts = [
        'input_data'   => 'array',
        'suggestion'   => 'array',
        'was_online'   => 'boolean',
        'was_accepted' => 'boolean',
        'created_at'   => 'datetime',
    ];
}
