# Laravel Outbound Mail Log

[![Tests](https://img.shields.io/github/actions/workflow/status/empinet/laravel-outbound-mail-log/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/empinet/laravel-outbound-mail-log/actions/workflows/run-tests.yml)
[![Quality](https://img.shields.io/github/actions/workflow/status/empinet/laravel-outbound-mail-log/quality.yml?branch=master&label=quality&style=flat-square)](https://github.com/empinet/laravel-outbound-mail-log/actions/workflows/quality.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/empinet/laravel-outbound-mail-log?style=flat-square)](https://packagist.org/packages/empinet/laravel-outbound-mail-log)
[![Total Downloads](https://img.shields.io/packagist/dt/empinet/laravel-outbound-mail-log?style=flat-square)](https://packagist.org/packages/empinet/laravel-outbound-mail-log)

Log outbound emails sent by Laravel into a database table.

This package listens to Laravel's mail sending events and stores outgoing email metadata (subject, recipients, sender, body, headers, attachments, mailable/notification class, mailer, and send status) so you can inspect what was sent.

## What gets logged

- subject
- from / to / cc / bcc
- HTML or text body (configurable)
- headers (configurable)
- attachment filenames
- mailable or notification class (when available)
- mailer name
- status (`sending` then `sent`)
- sent timestamp

## Installation

Install the package with Composer:

```bash
composer require empinet/laravel-outbound-mail-log
```

Publish and run the migration:

```bash
php artisan vendor:publish --tag="outbound-mail-log-migrations"
php artisan migrate
```

You can also use the default Laravel migration publish tag:

```bash
php artisan vendor:publish --tag="migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="outbound-mail-log-config"
```

You can also use the default Laravel config publish tag:

```bash
php artisan vendor:publish --tag="config"
```

## Configuration

The package ships disabled by default.

Set this in your `.env`:

```dotenv
OUTBOUND_MAIL_LOG_ENABLED=true
```

Available config options (`config/outbound-mail-log.php`):

```php
return [
    'enabled' => env('OUTBOUND_MAIL_LOG_ENABLED', false),
    'cleanup_records_after' => env('OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER', false),
    'log_headers' => env('OUTBOUND_MAIL_LOG_LOG_HEADERS', true),
    'log_body' => env('OUTBOUND_MAIL_LOG_LOG_BODY', true),
];
```

Recommended `.env` values for local/testing:

```dotenv
OUTBOUND_MAIL_LOG_ENABLED=true
OUTBOUND_MAIL_LOG_LOG_BODY=true
OUTBOUND_MAIL_LOG_LOG_HEADERS=true
OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER=false
```

For production, consider setting `OUTBOUND_MAIL_LOG_LOG_BODY=false` if emails may contain sensitive content.

## Usage

After installing, publishing migrations, and enabling the package, send mail normally using Laravel mailables/notifications.

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Hello from the app', function ($message): void {
    $message->to('user@example.com')
        ->from('noreply@example.com')
        ->subject('Test message');
});
```

Then inspect logs from your app:

```php
use Empinet\OutboundMailLog\Models\OutboundMailLog;

$latest = OutboundMailLog::query()
    ->latest('id')
    ->first();
```

## Cleanup command

To remove old records based on `OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER`, run:

```bash
php artisan outbound-mail-log:cleanup
```

Set `OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER=false` to disable cleanup.

To schedule cleanup daily, add this in your `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('outbound-mail-log:cleanup')->daily();
```

## Notes

- A log entry is created when sending starts (`sending`) and marked as `sent` after Laravel dispatches `MessageSent`.
- The package supports Mailables, Notification mail channel messages, and closure/raw emails.

## Testing

```bash
composer test
```

## Releasing

- Releases are tag-based.
- Every push to `master` triggers the `release` workflow and creates the next patch tag automatically.
- If no tag exists yet, the workflow bootstraps the first release at `v1.0.0`.
- You can also run the `release` workflow manually and pass an explicit version like `1.2.0`.
- Packagist updates are handled via Packagist auto-update integration.

If Composer shows `could not detect the root package version` in local development, that is normal before your first release tag. It does not affect package behavior.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). See [LICENSE](LICENSE.md).
