<?php

namespace App\Services;

use App\Models\FeatureFlag;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class FeatureService
{
    /**
     * Cache key prefix for feature flags.
     */
    protected const CACHE_PREFIX = 'feature_flag_';

    /**
     * Default labels for known feature keys.
     */
    protected array $defaultLabels = [
        'registration' => 'Registrasi Mandiri',
        'email_verification' => 'Verifikasi Email',
        'whatsapp_gateway' => 'WhatsApp Gateway',
        'whatsapp_verification' => 'Verifikasi WhatsApp OTP',
        'public_tracking' => 'Pelacakan Aduan Publik',
        'maintenance_mode' => 'Mode Pemeliharaan',
    ];

    /**
     * Check whether a specific feature flag is currently enabled.
     */
    public function isEnabled(string $key): bool
    {
        $cacheKey = self::CACHE_PREFIX.$key;

        return Cache::rememberForever($cacheKey, function () use ($key) {
            $flag = FeatureFlag::where('key', $key)->first();

            if ($flag !== null) {
                return (bool) $flag->is_enabled;
            }

            // Fallback default untuk environment atau konfigurasi awal
            if ($key === 'registration') {
                return (bool) config('auth.registration_enabled', false);
            }

            return false;
        });
    }

    /**
     * Enable a specific feature flag.
     */
    public function enable(string $key, ?int $userId = null): void
    {
        $flag = FeatureFlag::firstOrNew(['key' => $key]);
        $flag->label = $flag->label ?: ($this->defaultLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)));
        $flag->is_enabled = true;
        $flag->updated_by = ($userId && User::where('id', $userId)->exists()) ? $userId : null;
        $flag->save();

        $this->refreshCache($key, true);
    }

    /**
     * Disable a specific feature flag.
     */
    public function disable(string $key, ?int $userId = null): void
    {
        $flag = FeatureFlag::firstOrNew(['key' => $key]);
        $flag->label = $flag->label ?: ($this->defaultLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)));
        $flag->is_enabled = false;
        $flag->updated_by = ($userId && User::where('id', $userId)->exists()) ? $userId : null;
        $flag->save();

        $this->refreshCache($key, false);
    }

    /**
     * Toggle the status of a specific feature flag and return the new state.
     */
    public function toggle(string $key, int $userId): bool
    {
        $currentlyEnabled = $this->isEnabled($key);

        if ($currentlyEnabled) {
            $this->disable($key, $userId);

            return false;
        }

        $this->enable($key, $userId);

        return true;
    }

    /**
     * Clear or refresh the cache for a specific feature flag.
     */
    public function refreshCache(string $key, ?bool $value = null): void
    {
        $cacheKey = self::CACHE_PREFIX.$key;
        Cache::forget($cacheKey);

        if ($value !== null) {
            Cache::forever($cacheKey, $value);
        }
    }

    /**
     * Flush all feature flag caches.
     */
    public function clearAllCache(): void
    {
        $keys = FeatureFlag::pluck('key');

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX.$key);
        }
    }
}
