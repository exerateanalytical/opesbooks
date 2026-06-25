<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Feature flags: key => [label, description, default]. */
    public const FLAGS = [
        'ai_tax_suggestions' => ['AI Tax Suggestions',  'Surface AI-assisted tax categorisation hints.',        true],
        'dgi_auto_sync'      => ['DGI Auto-Sync',        'Automatically télétransmit invoices to Fiscalis/SIGIT.', true],
        'multi_currency'     => ['Multi-Currency (Beta)','Allow currencies beyond XAF.',                          false],
        'offline_sync'       => ['Offline Sync',         'Local-first capture with background synchronisation.',  true],
        'white_label'        => ['White-Label (Alpha)',  'Custom branding for Enterprise tenants.',               false],
        'maintenance_mode'   => ['Maintenance Mode',     'Show a maintenance banner to all tenants.',             false],
    ];

    public static function flag(string $key, bool $default = false): bool
    {
        return (bool) static::get($key, $default);
    }

    public static function get(string $key, $default = null)
    {
        $val = Cache::rememberForever("platform_setting:{$key}", function () use ($key) {
            return static::where('key', $key)->value('value');
        });

        if ($val === null) {
            return $default;
        }
        return $val === '1' || $val === 'true' ? true : ($val === '0' || $val === 'false' ? false : $val);
    }

    public static function set(string $key, $value): void
    {
        $stored = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        static::updateOrCreate(['key' => $key], ['value' => $stored]);
        Cache::forget("platform_setting:{$key}");
    }

    /** All feature flags resolved to their current boolean state. */
    public static function allFlags(): array
    {
        $out = [];
        foreach (self::FLAGS as $key => [$label, $desc, $default]) {
            $out[$key] = [
                'label'       => $label,
                'description' => $desc,
                'enabled'     => static::flag($key, $default),
            ];
        }
        return $out;
    }
}
