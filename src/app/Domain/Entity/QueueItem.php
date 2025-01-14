<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\WebhookStatus;
use Carbon\CarbonImmutable;

class QueueItem
{
    public function __construct(
        private ?int $id,
        private WebhookStatus $status,
        private int $webhookId,
        private CarbonImmutable $retryAt,
        private int $attempt,
        private Webhook $webhook,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): WebhookStatus
    {
        return $this->status;
    }

    public function setStatus(WebhookStatus $status): void
    {
        $this->status = $status;
    }

    public function getWebhookId(): int
    {
        return $this->webhookId;
    }

    public function setWebhookId(int $webhookId): void
    {
        $this->webhookId = $webhookId;
    }

    public function getRetryAt(): CarbonImmutable
    {
        return $this->retryAt;
    }

    public function setRetryAt(CarbonImmutable $retryAt): void
    {
        $this->retryAt = $retryAt;
    }

    public function getAttempt(): int
    {
        return $this->attempt;
    }

    public function setAttempt(int $attempt): void
    {
        $this->attempt = $attempt;
    }

    public function getWebhook(): Webhook
    {
        return $this->webhook;
    }

    public function setWebhook(Webhook $webhook): void
    {
        $this->webhook = $webhook;
    }
}
