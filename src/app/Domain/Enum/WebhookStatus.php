<?php

namespace App\Domain\Enum;

enum WebhookStatus: int
{
    case PENDING = 0;
    case SUCCESS = 1;
    case FAILED = 2;
}
