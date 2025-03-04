<?php

use Illuminate\Support\Facades\Route;
use Amirhf1\FeatureFlags\Http\Controllers\FeatureFlagsController;

Route::get('/feature-flags', [FeatureFlagsController::class, 'index']);
Route::post('/feature-flags/enable', [FeatureFlagsController::class, 'enable']);
Route::post('/feature-flags/disable', [FeatureFlagsController::class, 'disable']);
