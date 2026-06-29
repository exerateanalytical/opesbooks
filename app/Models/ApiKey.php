<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'company_id', 'name', 'environment', 'key_prefix', 'key_hash',
        'scopes', 'allowed_ips', 'rate_limit', 'status', 'last_used_at', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'scopes'       => 'array',
        'allowed_ips'  => 'array',
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    protected $hidden = ['key_hash'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requestLogs()
    {
        return $this->hasMany(ApiRequestLog::class);
    }

    /**
     * Generate a new plaintext key + its persisted record.
     * Returns [ApiKey $model, string $plainTextKey]. The plaintext is shown once.
     */
    public static function issue(array $attributes): array
    {
        $env    = ($attributes['environment'] ?? 'live') === 'test' ? 'test' : 'live';
        $secret = Str::random(40);
        $plain  = "ob_{$env}_sk_{$secret}";

        $model = static::create(array_merge($attributes, [
            'environment' => $env,
            'key_prefix'  => "ob_{$env}_sk_" . substr($secret, 0, 4),
            'key_hash'    => hash('sha256', $plain),
            'status'      => 'ACTIVE',
        ]));

        return [$model, $plain];
    }

    public function isUsable(): bool
    {
        return $this->status === 'ACTIVE'
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];
        return in_array('*', $scopes, true) || in_array($scope, $scopes, true);
    }

    /** Masked display form: env prefix + first 4 secret chars, e.g. ob_live_sk_3f9a••••. */
    public function maskedKey(): string
    {
        return $this->key_prefix . '••••';
    }
}
