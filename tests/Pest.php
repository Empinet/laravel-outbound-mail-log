<?php

use Empinet\OutboundMailLog\Tests\DisabledOutboundMailLogTestCase;
use Empinet\OutboundMailLog\Tests\TestCase;

uses(TestCase::class)->in('Feature/Enabled');
uses(DisabledOutboundMailLogTestCase::class)->in('Feature/Disabled');
