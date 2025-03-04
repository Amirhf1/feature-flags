<?php

namespace Amirhf1\FeatureFlags\Http\Middleware;

use Amirhf1\FeatureFlags;
use Closure;

class CheckFeatureFlag
{
    public function handle($request, Closure $next, $feature)
    {
        $user = $request->user();

        if (!FeatureFlags::isEnabled($feature, $user)) {
            return response()->json(['error' => 'Feature not available.'], 403);
        }

        return $next($request);
    }
}
