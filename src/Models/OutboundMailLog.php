<?php

namespace Empinet\OutboundMailLog\Models;

use Empinet\OutboundMailLog\Database\Factories\OutboundMailLogFactory;
use Empinet\OutboundMailLog\Enums\OutboundMailStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundMailLog extends Model
{
    use HasFactory;

    public const IDENTIFIER_HEADER_NAME = 'X-Outbound-Message-Id';

    protected $table = 'outbound_mail_logs';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'from' => 'array',
            'to' => 'array',
            'cc' => 'array',
            'bcc' => 'array',
            'headers' => 'array',
            'attachments' => 'array',
            'status' => OutboundMailStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function scopeOlderThanDays($query, int $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    public function markAsSent(): bool
    {
        return $this->update([
            'status' => OutboundMailStatus::SENT,
            'sent_at' => $this->sent_at ?? now(),
        ]);
    }

    public static function findByMailId(string $mailId): ?self
    {
        return self::query()->where('mail_id', $mailId)->first();
    }

    protected static function newFactory(): Factory
    {
        return OutboundMailLogFactory::new();
    }
}
