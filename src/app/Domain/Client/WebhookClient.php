<?php

declare(strict_types=1);

namespace App\Domain\Client;

use App\Domain\Entity\Webhook;

interface WebhookClient
{
    public function send(Webhook $webhook): bool;
}
