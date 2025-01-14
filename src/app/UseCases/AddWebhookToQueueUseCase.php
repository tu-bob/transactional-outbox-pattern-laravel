<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Domain\Entity\QueueItem;
use App\Domain\Entity\Webhook;
use App\Domain\Enum\WebhookStatus;
use App\Domain\Repository\QueueItemRepository;
use App\Domain\Repository\Repository;
use App\Domain\Repository\WebhookRepository;

class AddWebhookToQueueUseCase
{
    public function __construct(
        private readonly Repository $repository,
        private readonly WebhookRepository $webhookRepository,
        private readonly QueueItemRepository $queueItemRepository
    ) {
    }

    public function execute(string $url, int $orderId, string $name, string $event): void
    {
        $this->repository->startTransaction();
        try {
            $webhook = $this->createWebhook($url, $orderId, $name, $event);
            $this->addToQueue($webhook);
            $this->repository->commitTransaction();
        } catch (\Throwable $e) {
            $this->repository->rollbackTransaction();
            throw $e;
        }
    }

    private function createWebhook(string $url, int $orderId, string $name, string $event): Webhook
    {
        $webhook = new Webhook(
            id: null,
            url: $url,
            name: $name,
            event: $event,
            orderId: $orderId
        );

        $this->webhookRepository->save($webhook);

        return $webhook;
    }

    private function addToQueue(Webhook $webhook): void
    {
        $queueItem = new QueueItem(
            id: null,
            status: WebhookStatus::PENDING,
            webhookId: $webhook->getId(),
            retryAt: now()->toImmutable(),
            attempt: 0,
            webhook: $webhook
        );

        $this->queueItemRepository->save($queueItem);
    }
}
