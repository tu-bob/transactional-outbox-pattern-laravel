<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Repositories;

use App\Domain\Entity\QueueItem;
use App\Domain\Enum\WebhookStatus;
use App\Domain\Repository\QueueItemRepository as Repository;
use App\Infrastructure\Db\Mappers\QueueItemMapper;
use App\Infrastructure\Db\Mappers\WebhookMapper;
use App\Models\QueueItem as QueueItemModel;

readonly class QueueItemRepository implements Repository
{
    private const int MAX_FAILED_ATTEMPTS = 5;

    public function __construct(
        private QueueItemMapper $mapper,
        private WebhookMapper   $webhookMapper,
    ) {
    }

    public function findForUpdate(): ?QueueItem
    {
        $model = QueueItemModel::query()
            ->select('queue_items.*')
            ->with('webhook')
            ->join(
                'webhooks',
                'queue_items.webhook_id',
                '=',
                'webhooks.id'
            )
            ->leftJoin(
                'failed_attempts',
                'webhooks.url',
                '=',
                'failed_attempts.url'
            )
            ->where('status', WebhookStatus::PENDING)
            ->where('retry_at', '<=', now())
            ->whereRaw('coalesce(failed_attempts.attempts, 0) <= ?', [self::MAX_FAILED_ATTEMPTS])
            ->lockForUpdate()
            ->first();

        if ($model === null) {
            return null;
        }

        $entity = $this->mapper->toEntity($model);
        $entity->setWebhook(
            $this->webhookMapper->toEntity($model->webhook)
        );

        return $entity;
    }

    public function updateStatus(WebhookStatus $status, QueueItem $item): void
    {
        QueueItemModel::query()
            ->where('id', $item->getId())
            ->update(['status' => $status->value]);
    }

    public function save(QueueItem $item): void
    {
        $model = $this->mapper->toModel($item);
        $model->save();
    }
}
