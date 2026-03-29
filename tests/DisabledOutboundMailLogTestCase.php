<?php

namespace Empinet\OutboundMailLog\Tests;

class DisabledOutboundMailLogTestCase extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('outbound-mail-log.enabled', false);
    }
}
