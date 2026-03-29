<?php

namespace Empinet\OutboundMailLog\Tests\Support;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Test')
            ->greeting('Hello')
            ->line('This is a test notification')
            ->salutation('Best regards');
    }
}
