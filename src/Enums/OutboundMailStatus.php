<?php

namespace Empinet\OutboundMailLog\Enums;

enum OutboundMailStatus: string
{
    case SENDING = 'sending';
    case SENT = 'sent';
}
