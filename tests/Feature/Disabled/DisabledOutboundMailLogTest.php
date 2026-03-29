<?php

namespace Empinet\OutboundMailLog\Tests\Feature\Disabled;

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\DisabledOutboundMailLogTestCase;
use Empinet\OutboundMailLog\Tests\Support\TestMailable;
use Illuminate\Support\Facades\Mail;

class DisabledOutboundMailLogTest extends DisabledOutboundMailLogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
        $migration->up();
    }

    protected function tearDown(): void
    {
        OutboundMailLog::query()->delete();

        parent::tearDown();
    }

    public function test_does_not_log_emails_when_disabled(): void
    {
        $this->assertDatabaseCount('outbound_mail_logs', 0);

        Mail::to('recipient@example.com')->send(new TestMailable);

        $this->assertDatabaseCount('outbound_mail_logs', 0);
    }
}
