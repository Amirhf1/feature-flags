<?php

namespace Amirhf1\FeatureFlags;

use Amirhf1\FeatureFlags\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class FeatureFlags
{
    public function generateRandomPercentage(): int
    {
        return mt_rand(1, 100);
    }

    public function isEnabled(string $feature, $user = null): bool
    {
        // Check cache first
        if (Cache::has($feature)) {
            return Cache::get($feature);
        }

        // Check config
        $configFlags = Config::get('feature-flags.flags', []);
        if (isset($configFlags[$feature])) {
            $flag = $configFlags[$feature];

            // Check percentage rollout
            if (isset($flag['percentage']) && is_numeric($flag['percentage'])) {
                return $this->generateRandomPercentage() <= $flag['percentage'];
            }

            // Check user-specific rollout
            if (isset($flag['users']) && is_array($flag['users']) && $user) {
                return in_array($user->id, $flag['users']);
            }

            return $flag['enabled'] ?? false;
        }

        // Check database
        $flag = FeatureFlag::where('name', $feature)->first();
        if ($flag) {
            Cache::put($feature, $flag->enabled, now()->addMinutes(10));

            // Check user-specific flag from DB
            if ($user && $flag->users && in_array($user->id, json_decode($flag->users, true))) {
                return true;
            }

            // Check percentage rollout from DB
            if ($flag->percentage > 0) {
                return $this->generateRandomPercentage() <= $flag->percentage;
            }

            return $flag->enabled;
        }

        return false;
    }
}
