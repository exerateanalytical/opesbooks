<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled(string $key, ?\App\Models\Company $company = null)
 * @method static bool enabledFor(string $key, \App\Models\Company $company)
 * @method static void clearCache(string $key)
 * @see \App\Services\FeatureFlagService
 */
class Feature extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\FeatureFlagService::class;
    }
}
