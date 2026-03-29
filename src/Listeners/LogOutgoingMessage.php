<?php

namespace Empinet\OutboundMailLog\Listeners;

use Empinet\OutboundMailLog\Enums\OutboundMailStatus;
use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class LogOutgoingMessage
{
    public function handleSending(MessageSending $event): void
    {
        $event->message
            ->getHeaders()
            ->addHeader(OutboundMailLog::IDENTIFIER_HEADER_NAME, $id = $event->message->generateMessageId());

        $attachments = collect($event->message->getAttachments())
            ->map(fn ($attachment) => $attachment->getFilename())
            ->toArray();

        $data = [
            'mail_id' => $id,
            'subject' => $event->message->getSubject(),
            'to' => $this->parseAddress($event->message->getTo()),
            'from' => $this->parseAddress($event->message->getFrom()),
            'cc' => $this->parseAddress($event->message->getCc()),
            'bcc' => $this->parseAddress($event->message->getBcc()),
            'attachments' => $attachments,
            'status' => OutboundMailStatus::SENDING,
            'mailable' => $this->getMailable($event),
            'mailer' => $event->data['mailer'] ?? config('mail.default'),
        ];

        if (config('outbound-mail-log.log_body')) {
            $data['body'] = $event->message->getHtmlBody() ?? $event->message->getTextBody();
        }

        if (config('outbound-mail-log.log_headers')) {
            $data['headers'] = $event->message->getHeaders()->toArray();
        }

        OutboundMailLog::updateOrCreate(
            ['mail_id' => $id],
            $data
        );
    }

    public function handleSent(MessageSent $event): void
    {
        $header = $event->message->getHeaders()->get(OutboundMailLog::IDENTIFIER_HEADER_NAME);

        if ($header === null) {
            return;
        }

        $mailId = method_exists($header, 'getBodyAsString')
            ? $header->getBodyAsString()
            : $header->getBody();

        $entry = OutboundMailLog::findByMailId($mailId);

        $entry?->markAsSent();
    }

    private function getMailable(MessageSending $event): ?string
    {
        if (isset($event->data['__laravel_mailable'])) {
            return $event->data['__laravel_mailable'];
        }

        if (isset($event->data['__laravel_notification'])) {
            return $event->data['__laravel_notification'];
        }

        return null;
    }

    private function parseAddress(?array $addresses): array
    {
        if ($addresses === null) {
            return [];
        }

        return collect($addresses)
            ->map(fn (Address $address) => $address->getAddress())
            ->toArray();
    }
}
