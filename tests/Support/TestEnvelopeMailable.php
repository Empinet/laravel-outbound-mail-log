<?php

namespace Empinet\OutboundMailLog\Tests\Support;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestEnvelopeMailable extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Envelope Test',
            from: 'sender@example.com',
            cc: ['cc@example.com'],
            bcc: ['bcc@example.com'],
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h1>Envelope Test</h1>',
        );
    }
}
