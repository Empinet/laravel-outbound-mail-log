<?php

namespace Empinet\OutboundMailLog\Tests\Support;

use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;

class TestMailable extends Mailable
{
    public function build(): self
    {
        return $this->subject('Test')
            ->from('sender@example.com')
            ->bcc('bcc@example.com')
            ->cc('cc@example.com')
            ->html('<h1>Test</h1>');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(base_path('tests/Support/attachment.txt')),
        ];
    }
}
