<?php

namespace Empinet\OutboundMailLog\Database\Factories;

use Empinet\OutboundMailLog\Enums\OutboundMailStatus;
use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutboundMailLogFactory extends Factory
{
    protected $model = OutboundMailLog::class;

    public function definition(): array
    {
        return [
            'mailable' => 'App\\Mail\\TestMailable',
            'mail_id' => fake()->unique()->uuid().'@example.com',
            'subject' => fake()->sentence(),
            'from' => ['sender@example.com'],
            'to' => ['recipient@example.com'],
            'cc' => [],
            'bcc' => [],
            'body' => fake()->paragraph(),
            'status' => OutboundMailStatus::SENT->value,
            'headers' => null,
            'attachments' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function old(int $days = 31): self
    {
        return $this->state(fn () => [
            'created_at' => now()->subDays($days),
            'updated_at' => now()->subDays($days),
        ]);
    }
}
