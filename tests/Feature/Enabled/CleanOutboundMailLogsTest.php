<?php

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Illuminate\Support\Facades\Artisan;

beforeEach(function (): void {
    $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
    $migration->up();

    OutboundMailLog::factory()->count(1)->old(31)->create();
    OutboundMailLog::factory()->count(1)->old(14)->create([
        'subject' => 'Recent Email',
    ]);
});

afterEach(function (): void {
    OutboundMailLog::query()->delete();
});

test('it deletes logs older than the configured number of days', function (): void {
    config(['outbound-mail-log.cleanup_records_after' => 30]);

    $exitCode = Artisan::call('outbound-mail-log:cleanup');

    $this->assertDatabaseCount('outbound_mail_logs', 1);
    $this->assertDatabaseHas('outbound_mail_logs', [
        'subject' => 'Recent Email',
    ]);

    $output = Artisan::output();
    $this->assertStringContainsString('Deleted 1 outbound mail log records older than 30 days.', $output);
    $this->assertEquals(0, $exitCode);
});

test('it respects the cleanup_records_after configuration', function (): void {
    config(['outbound-mail-log.cleanup_records_after' => 10]);

    $exitCode = Artisan::call('outbound-mail-log:cleanup');

    $this->assertDatabaseCount('outbound_mail_logs', 0);

    $output = Artisan::output();
    $this->assertStringContainsString('Deleted 2 outbound mail log records older than 10 days.', $output);
    $this->assertEquals(0, $exitCode);
});

test('it does not delete any logs when cleanup is disabled', function (): void {
    config(['outbound-mail-log.cleanup_records_after' => false]);

    $exitCode = Artisan::call('outbound-mail-log:cleanup');

    $this->assertDatabaseCount('outbound_mail_logs', 2);

    $output = Artisan::output();
    $this->assertStringContainsString('Outbound mail log cleanup is disabled.', $output);
    $this->assertEquals(0, $exitCode);
});

test('it handles invalid cleanup configuration', function (): void {
    config(['outbound-mail-log.cleanup_records_after' => 'invalid']);

    $exitCode = Artisan::call('outbound-mail-log:cleanup');

    $this->assertDatabaseCount('outbound_mail_logs', 2);

    $output = Artisan::output();
    $this->assertStringContainsString('Invalid value for OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER.', $output);
    $this->assertEquals(1, $exitCode);
});
