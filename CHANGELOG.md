# Changelog

All notable changes to `empinet/laravel-outbound-mail-log` will be documented in this file.

## Unreleased

- Added `exclude_classes` configuration support to skip logging specific mailable
  and notification classes by exact fully-qualified class name.
- Improved `config/outbound-mail-log.php` with Laravel-style inline documentation
  for each configuration option.
- Documented Laravel 10 limitation for legacy `build()` mailables where class
  metadata may be unavailable during send events.
