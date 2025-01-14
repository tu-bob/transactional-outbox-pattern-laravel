<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Mappers;

use App\Domain\Entity\QueueItem;
use App\Models\QueueItem as QueueItemModel;

class QueueItemMapper
{
    public function toEntity(QueueItemModel $model): QueueItem
    {
        return new QueueItem(
            id: $model->id,
            status: $model->status,
            webhookId: $model->webhook_id,
            retryAt: $model->retry_at->toImmutable(),
            attempt: $model->attempt,
            webhook: new WebhookMapper()->toEntity($model->webhook),
        );
    }

    public function toModel(QueueItem $entity): QueueItemModel
    {
        $model = new QueueItemModel([
            'id' => $entity->getId(),
            'status' => $entity->getStatus(),
            'webhook_id' => $entity->getWebhookId(),
            'retry_at' => $entity->getRetryAt(),
            'attempt' => $entity->getAttempt(),
        ]);

        $model->exists = $entity->getId() !== null;

        return $model;
    }
}
