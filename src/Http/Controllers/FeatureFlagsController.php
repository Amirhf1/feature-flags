<?php

namespace Amirhf1\FeatureFlags\Http\Controllers;

use Illuminate\Http\Request;
use Amirhf1\FeatureFlags\Models\FeatureFlag;
use Illuminate\Routing\Controller;

class FeatureFlagsController extends Controller
{
    public function index()
    {
        return response()->json(FeatureFlag::all());
    }

    public function enable(Request $request)
    {
        $feature = $request->input('name');

        $flag = FeatureFlag::firstOrCreate(['name' => $feature]);
        $flag->enabled = true;
        $flag->save();

        return response()->json(['message' => "Feature '{$feature}' enabled."]);
    }

    public function disable(Request $request)
    {
        $feature = $request->input('name');
        $flag = FeatureFlag::where('name', $feature)->first();

        if (!$flag) {
            return response()->json(['error' => 'Feature not found.'], 404);
        }

        $flag->enabled = false;
        $flag->save();

        return response()->json(['message' => "Feature '{$feature}' disabled."]);
    }
}
