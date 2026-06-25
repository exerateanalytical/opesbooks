<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    public $timestamps = false;

    protected $fillable = ['metric_name', 'metric_value', 'recorded_at'];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'recorded_at'  => 'datetime',
    ];
}
