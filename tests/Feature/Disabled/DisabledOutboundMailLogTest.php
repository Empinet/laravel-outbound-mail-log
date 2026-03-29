<?php

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\Support\TestMailable;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
    $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
    $migration->up();
});

afterEach(function (): void {
    OutboundMailLog::query()->delete();
});

test('does not log emails when disabled', function (): void {
    $this->assertDatabaseCount('outbound_mail_logs', 0);

    Mail::to('recipient@example.com')->send(new TestMailable);

    $this->assertDatabaseCount('outbound_mail_logs', 0);
});
