<?php

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\Support\TestMailable;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\assertDatabaseEmpty;

beforeEach(function (): void {
    $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
    $migration->up();
});

afterEach(function (): void {
    OutboundMailLog::query()->delete();
});

test('does not log emails when disabled', function (): void {
    assertDatabaseEmpty('outbound_mail_logs');

    Mail::to('recipient@example.com')->send(new TestMailable);

    assertDatabaseEmpty('outbound_mail_logs');
});
