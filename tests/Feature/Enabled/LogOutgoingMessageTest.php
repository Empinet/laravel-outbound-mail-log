<?php

use Empinet\OutboundMailLog\Enums\OutboundMailStatus;
use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Empinet\OutboundMailLog\Tests\Support\TestMailable;
use Empinet\OutboundMailLog\Tests\Support\TestNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseEmpty;

beforeEach(function (): void {
    $migration = include __DIR__.'/../../../database/migrations/create_outbound_mail_logs_table.php.stub';
    $migration->up();
});

afterEach(function (): void {
    OutboundMailLog::query()->delete();
});

test('logs mailables', function (): void {
    assertDatabaseEmpty('outbound_mail_logs');

    Mail::to('recipient@example.com')->send(new TestMailable);

    expect($entry = OutboundMailLog::first())->toBeInstanceOf(OutboundMailLog::class)
        ->and($entry->to)->toContain('recipient@example.com')
        ->and($entry->subject)->toBe('Test')
        ->and($entry->from)->toContain('sender@example.com')
        ->and($entry->bcc)->toContain('bcc@example.com')
        ->and($entry->cc)->toContain('cc@example.com')
        ->and($entry->body)->toContain('<h1>Test</h1>')
        ->and($entry->attachments)->toContain('attachment.txt')
        ->and($entry->mailer)->toBe(config('mail.default'))
        ->and($entry->status)->toBe(OutboundMailStatus::SENT);
});

test('logs notifications', function (): void {
    Notification::route('mail', 'recipient@example.com')->notify(new TestNotification);

    expect($entry = OutboundMailLog::first())->toBeInstanceOf(OutboundMailLog::class)
        ->and($entry->to)->toContain('recipient@example.com')
        ->and($entry->mailable)->toBe(TestNotification::class);
});

test('logs emails sent via closure', function (): void {
    Mail::raw('This is the raw email body.', function ($message): void {
        $message->to('recipient@example.com')
            ->from('sender@example.com')
            ->subject('sending from closure');
    });

    expect($entry = OutboundMailLog::first())->toBeInstanceOf(OutboundMailLog::class)
        ->and($entry->to)->toContain('recipient@example.com')
        ->and($entry->subject)->toBe('sending from closure')
        ->and($entry->from)->toContain('sender@example.com');
});

test('does not store body when disabled', function (): void {
    config(['outbound-mail-log.log_body' => false]);

    Mail::to('recipient@example.com')->send(new TestMailable);

    expect($entry = OutboundMailLog::first())->toBeInstanceOf(OutboundMailLog::class)
        ->and($entry->body)->toBeNull();
});

test('does not store headers when disabled', function (): void {
    config(['outbound-mail-log.log_headers' => false]);

    Mail::to('recipient@example.com')->send(new TestMailable);

    expect($entry = OutboundMailLog::first())->toBeInstanceOf(OutboundMailLog::class)
        ->and($entry->headers)->toBeNull();
});
