# Feature Flags for Laravel

## معرفی
این پکیج یک سیستم ساده و انعطاف‌پذیر Feature Flag برای اپلیکیشن‌های لاراول فراهم می‌کند. به کمک این پکیج می‌توانید:
- فیچرها را به صورت داینامیک فعال/غیرفعال کنید
- فیچرها را برای درصد مشخصی از کاربران فعال کنید
- فیچرها را برای کاربران خاص محدود کنید
- از کش برای بهبود عملکرد استفاده کنید

## نصب

نصب از طریق Composer:

```bash
composer require amirhf1/feature-flags
```

## پیکربندی

انتشار فایل کانفیگ:

```bash
php artisan vendor:publish --tag=feature-flags-config
```

این دستور یک فایل `config/feature-flags.php` ایجاد می‌کند که می‌توانید feature flag ها را در آن تعریف کنید.

مثال کانفیگ:

```php
return [
    'flags' => [
        'new_feature' => [
            'enabled' => true,
            'percentage' => 50,     // فعال برای ۵۰ درصد کاربران
            'users' => [1, 2, 3],   // فعال فقط برای این کاربران
        ],
    ],
];
```

## اجرای مایگریشن‌ها

برای ایجاد جداول مورد نیاز در دیتابیس:

```bash
php artisan migrate
```

## نحوه استفاده

### بررسی وضعیت Feature Flag

استفاده از کلاس `FeatureFlags`:

```php
use Amirhf1\FeatureFlags\FeatureFlags;

$featureFlags = new FeatureFlags();
if ($featureFlags->isEnabled('new_feature', auth()->user())) {
    // فیچر فعال است
}
```

یا استفاده از helper function:

```php
if (feature_enabled('new_feature')) {
    // فیچر فعال است
}
```

### استفاده در Blade

```php
@if(feature_enabled('new_feature'))
    {{-- نمایش فیچر جدید --}}
@else
    {{-- نمایش نسخه قدیمی --}}
@endif
```

### Middleware

محافظت از روت‌ها با استفاده از middleware:

```php
Route::middleware(['feature-flag:new_feature'])->group(function () {
    Route::get('/new-feature', 'FeatureController@index');
});
```

### دستورات Artisan

فعال کردن یک فیچر:
```bash
php artisan feature-flags:enable new_feature
```

غیرفعال کردن یک فیچر:
```bash
php artisan feature-flags:disable new_feature
```

نمایش لیست تمام فیچرها:
```bash
php artisan feature-flags:list
```

## نکات مهم برای استفاده در Production

### پایداری نمایش فیچرها
این پکیج از یک الگوریتم پایدار برای نمایش فیچرها به درصد مشخصی از کاربران استفاده می‌کند. این یعنی:
- هر کاربر همیشه تجربه یکسانی خواهد داشت
- با رفرش کردن صفحه، وضعیت فیچر تغییر نمی‌کند
- توزیع واقعی کاربران دقیقاً مطابق با درصد تعیین شده خواهد بود

### کش‌کردن
برای بهبود پرفورمنس، نتایج به مدت ۱۰ دقیقه کش می‌شوند. می‌توانید از درایورهای مختلف کش مثل Redis یا Memcached استفاده کنید.

### مانیتورینگ
پیشنهاد می‌شود برای فیچرهای حساس، سیستم لاگینگ پیاده‌سازی کنید:

```php
Log::info('Feature flag check', [
    'feature' => 'new_feature',
    'user' => auth()->id(),
    'enabled' => feature_enabled('new_feature')
]);
```

### استراتژی Rollout
1. ابتدا فیچر را برای تیم داخلی فعال کنید
2. سپس برای درصد کمی از کاربران (مثلاً ۵٪) فعال کنید
3. به تدریج درصد را افزایش دهید
4. در صورت بروز مشکل، سریعاً فیچر را غیرفعال کنید

## اجرای تست‌ها

برای اجرای تست‌ها:

```bash
vendor/bin/phpunit
```

## نیازمندی‌ها
- PHP ^7.4 || ^8.0
- Laravel ^8.0 || ^9.0 || ^10.0

## لایسنس
این پکیج تحت لایسنس MIT منتشر شده است.

## مشارکت
از مشارکت شما در بهبود این پکیج استقبال می‌کنیم. لطفاً قبل از ارسال Pull Request، تست‌ها را اجرا کنید.
