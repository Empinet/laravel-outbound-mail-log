<?php

return [
    'enabled' => env('OUTBOUND_MAIL_LOG_ENABLED', false),

    'cleanup_records_after' => env('OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER', false),

    'log_headers' => env('OUTBOUND_MAIL_LOG_LOG_HEADERS', true),

    'log_body' => env('OUTBOUND_MAIL_LOG_LOG_BODY', true),
];
