<?php

namespace Amirhf1\FeatureFlags\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    protected $fillable = ['name', 'enabled', 'users', 'percentage', 'start_date', 'end_date'];
}
