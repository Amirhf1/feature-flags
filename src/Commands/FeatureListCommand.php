<?php

namespace Amirhf1\FeatureFlags\Commands;

use Amirhf1\FeatureFlags\Models\FeatureFlag;
use Illuminate\Console\Command;

class FeatureListCommand extends Command
{
    protected $signature = 'feature:list';
    protected $description = 'List all feature flags';

    public function handle()
    {
        $flags = FeatureFlag::all();

        $this->table(
            ['ID', 'Name', 'Enabled', 'Users', 'Percentage', 'Start Date', 'End Date'],
            $flags->toArray()
        );
    }
}
