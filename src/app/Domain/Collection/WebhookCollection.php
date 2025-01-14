<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\Entity\Webhook;
use Iterator;

class WebhookCollection implements Iterator
{
    private array $items;

    public function __construct(Webhook ...$webhooks)
    {
        $this->items = $webhooks;
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): int
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function current(): Webhook
    {
        return current($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
