<?php

namespace Amirhf1\FeatureFlags\Commands;

use Illuminate\Console\Command;
use Amirhf1\FeatureFlags\Models\FeatureFlag;

class FeatureEnableCommand extends Command
{
    protected $signature = 'feature:enable {name}';
    protected $description = 'Enable a feature flag';

    public function handle()
    {
        $name = $this->argument('name');
        $flag = FeatureFlag::firstOrCreate(['name' => $name]);
        $flag->enabled = true;
        $flag->save();

        $this->info("Feature flag '{$name}' enabled.");
    }
}
