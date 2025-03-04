<?php

use Amirhf1\FeatureFlags\Facades\FeatureFlags;

if (!function_exists('feature_enabled')) {
    function feature_enabled(string $feature, $user = null): bool
    {
        return FeatureFlags::isEnabled($feature, $user);
    }
}
