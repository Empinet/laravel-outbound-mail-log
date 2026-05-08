<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable outbound mail logging
    |--------------------------------------------------------------------------
    |
    | When enabled, outgoing emails are logged into the outbound_mail_logs
    | table. Set to false to fully disable event-based logging.
    |
    */
    'enabled' => env('OUTBOUND_MAIL_LOG_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Cleanup retention (in days)
    |--------------------------------------------------------------------------
    |
    | Number of days to keep outbound mail logs before cleanup removes older
    | records. Set to false to disable cleanup behavior.
    |
    */
    'cleanup_records_after' => env('OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER', false),

    /*
    |--------------------------------------------------------------------------
    | Store message headers
    |--------------------------------------------------------------------------
    |
    | Controls whether email headers are persisted in log records.
    |
    */
    'log_headers' => env('OUTBOUND_MAIL_LOG_LOG_HEADERS', true),

    /*
    |--------------------------------------------------------------------------
    | Store message body
    |--------------------------------------------------------------------------
    |
    | Controls whether email HTML/text body content is persisted in log
    | records. Consider disabling in production for sensitive emails.
    |
    */
    'log_body' => env('OUTBOUND_MAIL_LOG_LOG_BODY', true),

    /*
    |--------------------------------------------------------------------------
    | Excluded mail classes
    |--------------------------------------------------------------------------
    |
    | Exact fully-qualified class names for mailables or notifications that
    | should be excluded from logging.
    |
    */
    'exclude_classes' => [],
];
