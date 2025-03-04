<?php

namespace Amirhf1\FeatureFlags\Tests;

use Amirhf1\FeatureFlags\FeatureFlags;
use Amirhf1\FeatureFlags\Models\FeatureFlag;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Orchestra\Testbench\TestCase;
use Illuminate\Http\Request;

class FeatureFlagsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure migrations are run
        $this->loadMigrationsFrom([
            '--realpath' => true,
            '--path' => realpath(__DIR__ . '/../../database/migrations')
        ]);

        // Manually create the table if not exists
        if (!Schema::hasTable('feature_flags')) {
            Schema::create('feature_flags', function ($table) {
                $table->id();
                $table->string('name')->unique();
                $table->boolean('enabled')->default(false);
                $table->json('users')->nullable();
                $table->integer('percentage')->default(0);
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamps();
            });
        }
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite in memory
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Amirhf1\FeatureFlags\Providers\FeatureFlagsServiceProvider'
        ];
    }

    /** @test */
    public function it_can_create_a_feature_flag()
    {
        // Verify the table exists first
        $this->assertTrue(Schema::hasTable('feature_flags'), 'Feature flags table does not exist');

        $featureFlag = FeatureFlag::create([
            'name' => 'test_feature',
            'enabled' => true,
            'percentage' => 100,
            'users' => json_encode([1, 2, 3])
        ]);

        $this->assertDatabaseHas('feature_flags', [
            'name' => 'test_feature',
            'enabled' => true,
            'percentage' => 100
        ]);
    }

    /** @test */
    public function it_checks_feature_flag_from_config()
    {
        // Set up config
        Config::set('feature-flags.flags.new_feature', [
            'enabled' => true,
            'percentage' => 100,
            'users' => [1, 2, 3]
        ]);

        $featureFlags = new FeatureFlags();

        // Create a mock user
        $user = new class extends User {
            public $id = 1;
        };

        $this->assertTrue($featureFlags->isEnabled('new_feature', $user));
    }

    /** @test */
    public function it_checks_feature_flag_from_database()
    {
        // Create a feature flag in the database
        $featureFlag = FeatureFlag::create([
            'name' => 'database_feature',
            'enabled' => true,
            'percentage' => 100,
            'users' => json_encode([1, 2, 3])
        ]);

        $featureFlags = new FeatureFlags();

        // Create a mock user
        $user = new class extends User {
            public $id = 1;
        };

        $this->assertTrue($featureFlags->isEnabled('database_feature', $user));
    }

    /** @test */
    public function it_respects_percentage_rollout()
    {
        // Create a feature flag with 50% rollout
        $featureFlag = FeatureFlag::create([
            'name' => 'percentage_feature',
            'enabled' => true,
            'percentage' => 50
        ]);

        // Mock the random number generation to test percentage rollout
        $featureFlags = Mockery::mock(FeatureFlags::class)->makePartial();

        // Seed with specific random values to test percentage distribution
        $randomSequence = [
            30, 60, 40, 20, 70,
            10, 90, 50, 45, 55
        ];
        $callCount = 0;

        $featureFlags->shouldReceive('generateRandomPercentage')
            ->andReturnUsing(function() use (&$callCount, $randomSequence) {
                $value = $randomSequence[$callCount % count($randomSequence)];
                $callCount++;
                return $value;
            });

        // Run multiple times to check percentage distribution
        $enabledCount = 0;
        $totalTrials = count($randomSequence);

        for ($i = 0; $i < $totalTrials; $i++) {
            if ($featureFlags->isEnabled('percentage_feature')) {
                $enabledCount++;
            }
        }

        // Check if the enabled percentage is close to the specified 50%
        $actualPercentage = ($enabledCount / $totalTrials) * 100;
        $this->assertGreaterThan(30, $actualPercentage);
        // $this->assertLessThan(70, $actualPercentage);
    }
    /** @test */
    public function it_returns_false_for_non_existent_feature()
    {
        $featureFlags = new FeatureFlags();
        $this->assertFalse($featureFlags->isEnabled('non_existent_feature'));
    }

    /** @test */
    public function helper_function_works_correctly()
    {
        // Create a feature flag
        FeatureFlag::create([
            'name' => 'helper_feature',
            'enabled' => true
        ]);

        $this->assertTrue(feature_enabled('helper_feature'));
    }
}
