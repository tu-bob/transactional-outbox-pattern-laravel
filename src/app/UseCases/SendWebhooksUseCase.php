<?php

declare(strict_types=1);

namespace App\UseCases;

use App\Domain\Client\WebhookClient;
use App\Domain\Entity\FailedAttempt;
use App\Domain\Entity\QueueItem;
use App\Domain\Enum\WebhookStatus;
use App\Domain\Repository\FailedAttemptRepository;
use App\Domain\Repository\QueueItemRepository;
use App\Domain\Repository\Repository;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class SendWebhooksUseCase
{
    private const int MAX_RETRY_DELAY_IN_SECONDS = 60;

    public function __construct(
        private WebhookClient $client,
        private Repository $repository,
        private QueueItemRepository $queueItemRepository,
        private FailedAttemptRepository $failedAttemptRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(): void
    {
        $this->logger->info('Starting to process webhooks');
        $run = true;
        $count = 0;

        while ($run) {
            $this->repository->startTransaction();

            try {
                $item = $this->queueItemRepository->findForUpdate();

                $this->logger->info('Processing webhook', ['webhook' => $item]);

                if ($item === null) {
                    $this->logger->info('No more webhooks to process');
                    $run = false;
                    $this->repository->commitTransaction();
                    continue;
                }

                $this->sendWebhook($item);
            } catch (Throwable $e) {
                $this->logger->error('Error while sending webhooks', ['exception' => $e, 'webhook' => $item]);
                $this->repository->rollbackTransaction();
            }

            $this->repository->commitTransaction();
            $count++;

            $this->logger->info('Processed ' . $count . ' webhooks', ['webhook' => $item]);
        }

        $this->logger->info('Total processed webhooks: ' . $count);
    }

    private function sendWebhook(QueueItem $item): void
    {
        $result = $this->client->send($item->getWebhook());

        $this->logger->info('Request result', ['webhook' => $item, 'result' => $result]);

        $status = $result ? WebhookStatus::SUCCESS : WebhookStatus::FAILED;
        $this->queueItemRepository->updateStatus($status, $item);

        if ($status === WebhookStatus::FAILED) {
            $this->handleFailedWebhook($item);
        }
    }

    private function handleFailedWebhook(QueueItem $item): void
    {
        $failedAttempt = new FailedAttempt(
            id: null,
            url: $item->getWebhook()->getUrl(),
            attempts: $item->getAttempt() + 1,
        );
        $this->failedAttemptRepository->save($failedAttempt);
        $this->scheduleRetry($item);
    }

    private function scheduleRetry(QueueItem $item): void
    {
        $retryDelay = 2 ** $item->getAttempt();

        if ($retryDelay > self::MAX_RETRY_DELAY_IN_SECONDS) {
            $this->logger->info('Max retry delay reached', ['webhook' => $item]);
            return;
        }

        $nextAttempt = new QueueItem(
            id: null,
            status: WebhookStatus::PENDING,
            webhookId: $item->getWebhookId(),
            retryAt: now()->addSeconds($retryDelay)->toImmutable(),
            attempt: $item->getAttempt() + 1,
            webhook: $item->getWebhook(),
        );

        $this->queueItemRepository->save($nextAttempt);

        $this->logger->info('Scheduled retry', ['webhook' => $item, 'nextAttempt' => $nextAttempt]);
    }
}
