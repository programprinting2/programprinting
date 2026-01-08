<?php

namespace App\Services;

use App\Models\MasterParameter;
use Illuminate\Support\Facades\Cache;

class ParameterService
{
    /**
     * Get cached MasterParameter by name
     */
    public static function getMasterParameter(string $name): ?MasterParameter
    {
        return Cache::remember("master_parameter_{$name}", 3600, function () use ($name) {
            return MasterParameter::where('nama_parameter', $name)->first();
        });
    }

    /**
     * Get cached parameter details
     */
    public static function getParameterDetails(string $parameterName): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("parameter_details_{$parameterName}", 3600, function () use ($parameterName) {
            $master = self::getMasterParameter($parameterName);
            return $master ? $master->details()->where('aktif', 1)->get() : collect();
        });
    }

    /**
     * Clear parameter cache
     */
    public static function clearCache(): void
    {
        Cache::flush(); // Or more specific cache clearing
    }
}