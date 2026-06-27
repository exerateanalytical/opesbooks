<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Firm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'oecam_number',
        'email', 'phone', 'address', 'city',
        'logo_path', 'max_clients', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'max_clients' => 'integer',
    ];

    /** Companies managed by this firm. */
    public function clients(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'firm_clients')
                    ->withPivot(['assigned_accountant_id', 'engagement_type', 'billing_mode', 'notes', 'is_active', 'onboarded_at', 'locked_until'])
                    ->withTimestamps();
    }

    /** Active client companies. */
    public function activeClients(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->clients()->wherePivot('is_active', true);
    }

    /** Staff members belonging to this firm. */
    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'firm_users')
                    ->withPivot(['firm_role', 'is_active'])
                    ->withTimestamps();
    }

    /** Active staff. */
    public function activeStaff(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->staff()->wherePivot('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function clientCount(): int
    {
        return $this->activeClients()->count();
    }

    public function isAtCapacity(): bool
    {
        return $this->clientCount() >= $this->max_clients;
    }
}
