<?php

namespace Empinet\OutboundMailLog\Tests\Feature\Enabled;

use Empinet\OutboundMailLog\Enums\OutboundMailStatus;
use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\Support\TestMailable;
use Empinet\OutboundMailLog\Tests\Support\TestNotification;
use Empinet\OutboundMailLog\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class LogOutgoingMessageTest extends TestCase
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

    public function test_logs_mailables(): void
    {
        $this->assertDatabaseCount('outbound_mail_logs', 0);

        Mail::to('recipient@example.com')->send(new TestMailable);

        $entry = OutboundMailLog::first();
        $this->assertInstanceOf(OutboundMailLog::class, $entry);
        $this->assertContains('recipient@example.com', $entry->to);
        $this->assertSame('Test', $entry->subject);
        $this->assertContains('sender@example.com', $entry->from);
        $this->assertContains('bcc@example.com', $entry->bcc);
        $this->assertContains('cc@example.com', $entry->cc);
        $this->assertStringContainsString('<h1>Test</h1>', (string) $entry->body);
        $this->assertContains('attachment.txt', $entry->attachments);
        $this->assertSame(config('mail.default'), $entry->mailer);
        $this->assertSame(OutboundMailStatus::SENT, $entry->status);
    }

    public function test_logs_notifications(): void
    {
        Notification::route('mail', 'recipient@example.com')->notify(new TestNotification);

        $entry = OutboundMailLog::first();
        $this->assertInstanceOf(OutboundMailLog::class, $entry);
        $this->assertContains('recipient@example.com', $entry->to);
        $this->assertSame(TestNotification::class, $entry->mailable);
    }

    public function test_logs_emails_sent_via_closure(): void
    {
        Mail::raw('This is the raw email body.', function ($message): void {
            $message->to('recipient@example.com')
                ->from('sender@example.com')
                ->subject('sending from closure');
        });

        $entry = OutboundMailLog::first();
        $this->assertInstanceOf(OutboundMailLog::class, $entry);
        $this->assertContains('recipient@example.com', $entry->to);
        $this->assertSame('sending from closure', $entry->subject);
        $this->assertContains('sender@example.com', $entry->from);
    }

    public function test_does_not_store_body_when_disabled(): void
    {
        config(['outbound-mail-log.log_body' => false]);

        Mail::to('recipient@example.com')->send(new TestMailable);

        $entry = OutboundMailLog::first();
        $this->assertInstanceOf(OutboundMailLog::class, $entry);
        $this->assertNull($entry->body);
    }

    public function test_does_not_store_headers_when_disabled(): void
    {
        config(['outbound-mail-log.log_headers' => false]);

        Mail::to('recipient@example.com')->send(new TestMailable);

        $entry = OutboundMailLog::first();
        $this->assertInstanceOf(OutboundMailLog::class, $entry);
        $this->assertNull($entry->headers);
    }
}
