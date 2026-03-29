<?php

namespace Empinet\OutboundMailLog\Tests\Feature\Enabled;

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class CleanOutboundMailLogsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
        $migration->up();

        OutboundMailLog::factory()->count(1)->old(31)->create();
        OutboundMailLog::factory()->count(1)->old(14)->create([
            'subject' => 'Recent Email',
        ]);
    }

    protected function tearDown(): void
    {
        OutboundMailLog::query()->delete();

        parent::tearDown();
    }

    public function test_it_deletes_logs_older_than_the_configured_number_of_days(): void
    {
        config(['outbound-mail-log.cleanup_records_after' => 30]);

        $exitCode = Artisan::call('outbound-mail-log:cleanup');

        $this->assertDatabaseCount('outbound_mail_logs', 1);
        $this->assertDatabaseHas('outbound_mail_logs', ['subject' => 'Recent Email']);
        $this->assertStringContainsString('Deleted 1 outbound mail log records older than 30 days.', Artisan::output());
        $this->assertEquals(0, $exitCode);
    }

    public function test_it_respects_the_cleanup_records_after_configuration(): void
    {
        config(['outbound-mail-log.cleanup_records_after' => 10]);

        $exitCode = Artisan::call('outbound-mail-log:cleanup');

        $this->assertDatabaseCount('outbound_mail_logs', 0);
        $this->assertStringContainsString('Deleted 2 outbound mail log records older than 10 days.', Artisan::output());
        $this->assertEquals(0, $exitCode);
    }

    public function test_it_does_not_delete_any_logs_when_cleanup_is_disabled(): void
    {
        config(['outbound-mail-log.cleanup_records_after' => false]);

        $exitCode = Artisan::call('outbound-mail-log:cleanup');

        $this->assertDatabaseCount('outbound_mail_logs', 2);
        $this->assertStringContainsString('Outbound mail log cleanup is disabled.', Artisan::output());
        $this->assertEquals(0, $exitCode);
    }

    public function test_it_handles_invalid_cleanup_configuration(): void
    {
        config(['outbound-mail-log.cleanup_records_after' => 'invalid']);

        $exitCode = Artisan::call('outbound-mail-log:cleanup');

        $this->assertDatabaseCount('outbound_mail_logs', 2);
        $this->assertStringContainsString('Invalid value for OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER.', Artisan::output());
        $this->assertEquals(1, $exitCode);
    }
}
