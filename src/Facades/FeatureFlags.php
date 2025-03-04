<?php

namespace Amirhf1\FeatureFlags\Facades;

use Illuminate\Support\Facades\Facade;

class FeatureFlags extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'feature-flags';
    }
}
