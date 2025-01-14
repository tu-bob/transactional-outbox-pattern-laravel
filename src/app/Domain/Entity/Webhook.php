<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class Webhook
{
    public function __construct(
        private ?int $id,
        private string $url,
        private string $name,
        private string $event,
        private int $orderId,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
