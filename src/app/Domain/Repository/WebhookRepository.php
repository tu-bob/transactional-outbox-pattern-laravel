<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Webhook;

interface WebhookRepository
{
    public function save(Webhook $webhook): void;
}
