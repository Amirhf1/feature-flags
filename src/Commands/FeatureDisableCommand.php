<?php

namespace Amirhf1\FeatureFlags\Commands;

use Illuminate\Console\Command;
use Amirhf1\FeatureFlags\Models\FeatureFlag;

class FeatureDisableCommand extends Command
{
    protected $signature = 'feature:disable {name}';
    protected $description = 'Disable a feature flag';

    public function handle()
    {
        $name = $this->argument('name');
        $flag = FeatureFlag::where('name', $name)->first();

        if (!$flag) {
            $this->error("Feature flag '{$name}' not found.");
            return;
        }

        $flag->enabled = false;
        $flag->save();

        $this->info("Feature flag '{$name}' disabled.");
    }
}
