# Feature Flags for Laravel

## Introduction

This package provides a simple and flexible feature flag system for Laravel applications. It allows developers to enable or disable features dynamically, roll out features to a percentage of users, or restrict features to specific users.

## Installation

You can install the package via Composer:

```bash
composer require amirhf1/feature-flags
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=feature-flags-config
```

This will create a `config/feature-flags.php` file where you can define feature flags.

Example configuration:

```php
return [
    'flags' => [
        'new_feature' => [
            'enabled' => true,
            'percentage' => 50,     // Only enable for 50% of users
            'users' => [1, 2, 3],   // Enable only for specific user IDs
        ],
    ],
];
```

## Running Migrations

Run the database migrations to create the necessary tables:

```bash
php artisan migrate
```

## Usage

### Checking Feature Flags

You can check if a feature is enabled in your application using the `FeatureFlags` class or helper function:

```php
use Amirhf1\FeatureFlags\FeatureFlags;

$featureFlags = new FeatureFlags();
if ($featureFlags->isEnabled('new_feature', auth()->user())) {
    // Feature is enabled
}
```

Or use the helper function:

```php
if (feature_enabled('new_feature')) {
    // Feature is enabled
}
```

### Middleware

To protect routes based on feature flags, you can use the provided middleware:

```php
Route::middleware(['feature-flag:new_feature'])->get('/new-feature', function () {
    return 'This feature is enabled!';
});
```

### Artisan Commands

Enable a feature:

```bash
php artisan feature-flags:enable new_feature
```

Disable a feature:

```bash
php artisan feature-flags:disable new_feature
```

List all feature flags:

```bash
php artisan feature-flags:list
```

## Running Tests

You can run tests with:

```bash
vendor/bin/phpunit
```
