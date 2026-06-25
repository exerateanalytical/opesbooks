<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'type', 'target_plan', 'target_company_id',
        'published_at', 'expires_at', 'active', 'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'active'       => 'boolean',
    ];

    public function targetCompany()
    {
        return $this->belongsTo(Company::class, 'target_company_id');
    }

    /** Currently-visible announcements for a given company + plan. */
    public function scopeVisible($query, ?int $companyId = null, ?string $plan = null)
    {
        return $query->where('active', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('target_company_id')->orWhere('target_company_id', $companyId))
            ->where(fn ($q) => $q->whereNull('target_plan')->orWhere('target_plan', $plan));
    }
}
