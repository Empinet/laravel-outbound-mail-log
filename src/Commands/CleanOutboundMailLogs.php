<?php

namespace Empinet\OutboundMailLog\Commands;

use Empinet\OutboundMailLog\Models\OutboundMailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CleanOutboundMailLogs extends Command
{
    protected $signature = 'outbound-mail-log:cleanup';

    protected $description = 'Remove old outbound mail logs based on the configured retention period.';

    public function handle(): int
    {
        $days = Config::get('outbound-mail-log.cleanup_records_after');

        if ($days === false) {
            $this->info('Outbound mail log cleanup is disabled. Set OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER to a number of days to enable.');

            return self::SUCCESS;
        }

        if (! is_numeric($days) || $days < 1) {
            $this->error('Invalid value for OUTBOUND_MAIL_LOG_CLEANUP_RECORDS_AFTER. Please set a number of days or false to disable.');

            return self::FAILURE;
        }

        $deleted = OutboundMailLog::olderThanDays((int) $days)->delete();

        $this->info("Deleted {$deleted} outbound mail log records older than {$days} days.");

        return self::SUCCESS;
    }
}
