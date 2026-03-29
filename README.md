# Laravel Outbound Mail Log

[![Tests](https://img.shields.io/github/actions/workflow/status/empinet/laravel-outbound-mail-log/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/empinet/laravel-outbound-mail-log/actions/workflows/run-tests.yml)
[![Quality](https://img.shields.io/github/actions/workflow/status/empinet/laravel-outbound-mail-log/quality.yml?branch=master&label=quality&style=flat-square)](https://github.com/empinet/laravel-outbound-mail-log/actions/workflows/quality.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/empinet/laravel-outbound-mail-log?style=flat-square)](https://packagist.org/packages/empinet/laravel-outbound-mail-log)
[![Total Downloads](https://img.shields.io/packagist/dt/empinet/laravel-outbound-mail-log?style=flat-square)](https://packagist.org/packages/empinet/laravel-outbound-mail-log)

Log outbound emails sent by Laravel into a database table.

This package listens to Laravel's mail sending events and stores outgoing email metadata (subject, recipients, sender, body, headers, attachments, mailable/notification class, mailer, and send status) so you can inspect what was sent.

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

Publish the config file:

```bash
php artisan vendor:publish --tag="outbound-mail-log-config"
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

## Cleanup command

To remove old records based on `OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER`, run:

```bash
php artisan outbound-mail-log:cleanup
```

Set `OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER=false` to disable cleanup.

## Notes

- A log entry is created when sending starts (`sending`) and marked as `sent` after Laravel dispatches `MessageSent`.
- The package supports Mailables, Notification mail channel messages, and closure/raw emails.

## Testing

```bash
composer test
```

## Releasing

- Releases are tag-based.
- Use the GitHub Actions `release` workflow and provide a version like `0.1.0`.
- The workflow creates a `v0.1.0` tag, creates a GitHub release, and can notify Packagist if `PACKAGIST_UPDATE_TOKEN` is configured in repository secrets.

If Composer shows `could not detect the root package version` in local development, that is normal before your first release tag. It does not affect package behavior.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). See [LICENSE](LICENSE.md).
