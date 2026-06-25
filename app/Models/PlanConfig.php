<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanConfig extends Model
{
    protected $fillable = [
        'name', 'slug', 'price_xaf_monthly', 'price_xaf_yearly', 'max_users',
        'max_invoices_per_month', 'api_calls_per_hour', 'features', 'is_active', 'sort_order',
    ];

    protected $casts = ['features' => 'array', 'is_active' => 'boolean'];
}
