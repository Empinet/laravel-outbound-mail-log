<?php

namespace Empinet\OutboundMailLog;

use Empinet\OutboundMailLog\Commands\CleanOutboundMailLogs;
use Empinet\OutboundMailLog\Listeners\LogOutgoingMessage;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class OutboundMailLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/outbound-mail-log.php', 'outbound-mail-log');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/outbound-mail-log.php' => config_path('outbound-mail-log.php'),
            ], 'outbound-mail-log-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_outbound_mail_logs_table.php.stub' => database_path('migrations/'.$this->migrationFileName('create_outbound_mail_logs_table.php')),
            ], 'outbound-mail-log-migrations');

            $this->commands([
                CleanOutboundMailLogs::class,
            ]);
        }

        if (! config('outbound-mail-log.enabled')) {
            return;
        }

        Event::listen(
            MessageSending::class,
            fn (MessageSending $event) => app(LogOutgoingMessage::class)->handleSending($event)
        );

        Event::listen(
            MessageSent::class,
            fn (MessageSent $event) => app(LogOutgoingMessage::class)->handleSent($event)
        );
    }

    private function migrationFileName(string $migrationName): string
    {
        return date('Y_m_d_His').'_'.$migrationName;
    }
}
