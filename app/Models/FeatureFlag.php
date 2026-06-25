<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    protected $fillable = ['key', 'name', 'description', 'enabled_for', 'specific_company_ids'];

    protected $casts = ['specific_company_ids' => 'array'];

    /** Plan rank for plan_*_plus gating. */
    public const PLAN_RANK = ['free' => 0, 'starter' => 1, 'business' => 2, 'enterprise' => 3];
}
