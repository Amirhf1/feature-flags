<?php

namespace Amirhf1\FeatureFlags\Providers;

use Amirhf1\FeatureFlags\FeatureFlags;
use Illuminate\Support\ServiceProvider;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('feature-flags', function ($app) {
            return new FeatureFlags();
        });

        $this->mergeConfigFrom(__DIR__ . '/../../config/feature-flags.php', 'feature-flags');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/feature-flags.php' => config_path('feature-flags.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../../database/migrations/2023_01_01_create_feature_flags_table.php'
                => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_feature_flags_table.php'),
            ], 'migrations');
        }
    }
}
