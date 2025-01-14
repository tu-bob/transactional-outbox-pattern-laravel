<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\QueueItem;
use App\Domain\Enum\WebhookStatus;

interface QueueItemRepository
{
    public function findForUpdate(): ?QueueItem;

    public function updateStatus(WebhookStatus $status, QueueItem $item): void;

    public function save(QueueItem $item): void;
}
