<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'assigned_caisse_code',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /** Companies this user may access (active one is company_id). */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role', 'is_default')
            ->withTimestamps();
    }

    /** This user's role within a given company (from the pivot). */
    public function roleInCompany(int $companyId): ?string
    {
        return $this->companies()->where('companies.id', $companyId)->first()?->pivot->role;
    }

    public function belongsToCompany(int $companyId): bool
    {
        return $this->companies()->where('companies.id', $companyId)->exists();
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function isClerk(): bool      { return $this->role === 'CLERK'; }
    public function isAccountant(): bool { return $this->role === 'ACCOUNTANT'; }
    public function isOwner(): bool      { return $this->role === 'OWNER'; }
    public function isSuperAdmin(): bool { return $this->role === 'SUPER_ADMIN'; }

    public function activeCaisseCode(): string
    {
        return $this->assigned_caisse_code ?? '571100';
    }
}
